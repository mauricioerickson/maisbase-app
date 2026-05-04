<?php

// filepath: app/Livewire/Admin/Financial/Dashboard.php

namespace App\Livewire\Admin\Financial;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Athlete;
use Carbon\Carbon;

class Dashboard extends Component
{
    use \Mary\Traits\Toast;
    use \Livewire\WithPagination;

    // Listagem e Filtros
    public $search = '';

    // Gestão de Baixa
    public bool $showPaymentModal = false;
    public $paymentInvoiceId;
    public $paymentDate;
    public $paymentMethod = 'pix';
    public $selectedInvoiceAmount = 0;
    public $selectedInvoiceAthlete = '';

    public function render()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Métricas do Mês
        $totalReceived = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalPending = Invoice::where('status', 'pending')
            ->sum('amount');

        $overdueCount = Invoice::where('status', 'pending')
            ->where('due_date', '<', Carbon::now()->startOfDay())
            ->count();

        // Lista de Faturas Recentes/Pendentes
        $invoices = Invoice::with('athlete')
            ->when($this->search, function($q) {
                $q->whereHas('athlete', fn($aq) => $aq->where('name', 'like', "%{$this->search}%"));
            })
            ->latest('due_date')
            ->paginate(10);

        // Dados para Gráfico Simples
        $chartData = [
            'labels' => ['Recebido', 'Pendente'],
            'datasets' => [
                [
                    'label' => 'Financeiro (R$)',
                    'data' => [$totalReceived, $totalPending],
                    'backgroundColor' => ['#2E7D32', '#B00020'],
                ]
            ]
        ];

        return view('livewire.admin.financial.dashboard', [
            'totalReceived' => $totalReceived,
            'totalPending' => $totalPending,
            'overdueCount' => $overdueCount,
            'chartData' => $chartData,
            'invoices' => $invoices,
            'paymentMethods' => [
                ['id' => 'pix', 'name' => 'PIX'],
                ['id' => 'dinheiro', 'name' => 'Dinheiro'],
                ['id' => 'cartao', 'name' => 'Cartão'],
                ['id' => 'transferencia', 'name' => 'Transferência'],
            ]
        ])->layout('layouts.app');
    }

    /**
     * Abre o modal de confirmação de pagamento.
     */
    public function confirmPayment($id)
    {
        $invoice = Invoice::with('athlete')->findOrFail($id);
        $this->paymentInvoiceId = $invoice->id;
        $this->paymentDate = now()->format('Y-m-d');
        $this->selectedInvoiceAmount = $invoice->amount;
        $this->selectedInvoiceAthlete = $invoice->athlete->name;
        $this->showPaymentModal = true;
    }

    /**
     * Realiza a baixa manual de um recebimento com detalhes.
     */
    public function processPayment()
    {
        $invoice = Invoice::findOrFail($this->paymentInvoiceId);
        $invoice->update([
            'status' => 'paid',
            'paid_at' => $this->paymentDate,
            // Poderíamos salvar o método em uma coluna nova se necessário, 
            // mas por enquanto vamos focar no status e data.
        ]);

        $this->showPaymentModal = false;
        $this->success('Recebimento de ' . $this->selectedInvoiceAthlete . ' confirmado!');
    }

    /**
     * Remove uma fatura (Cancelamento).
     */
    public function delete($id)
    {
        Invoice::findOrFail($id)->delete();
        $this->success('Fatura cancelada.');
    }

    /**
     * Gera faturas para todos os atletas ativos que ainda não possuem fatura no mês corrente.
     */
    public function generateMonthlyInvoices()
    {
        $athletes = Athlete::where('status', 'ativo')
            ->whereNotNull('subscription_plan_id')
            ->with('subscriptionPlan')
            ->get();

        $count = 0;
        $month = now()->month;
        $year = now()->year;

        foreach ($athletes as $athlete) {
            // Verifica se já existe fatura para este mês/ano
            $exists = Invoice::where('athlete_id', $athlete->id)
                ->whereMonth('due_date', $month)
                ->whereYear('due_date', $year)
                ->exists();

            if (!$exists) {
                $plan = $athlete->subscriptionPlan;
                
                Invoice::create([
                    'athlete_id' => $athlete->id,
                    'amount' => $plan->amount,
                    'due_date' => now()->setDay($plan->due_day),
                    'status' => 'pending',
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->success("Processamento concluído! {$count} novas faturas geradas.");
        } else {
            $this->info("Nenhuma fatura pendente de geração para este mês.");
        }
    }
}
