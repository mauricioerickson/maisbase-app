<?php

// filepath: app/Livewire/Admin/Financial/Dashboard.php

namespace App\Livewire\Admin\Financial;

use Livewire\Component;
use App\Models\Invoice;
use Carbon\Carbon;

class Dashboard extends Component
{
    use \Mary\Traits\Toast;

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
        ])->layout('layouts.app');
    }

    /**
     * Realiza a baixa manual de um recebimento.
     */
    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->success('Recebimento confirmado com sucesso!');
    }

    /**
     * Remove uma fatura (Cancelamento).
     */
    public function delete($id)
    {
        Invoice::findOrFail($id)->delete();
        $this->success('Fatura cancelada.');
    }
}
