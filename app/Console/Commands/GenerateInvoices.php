<?php

// filepath: app/Console/Commands/GenerateInvoices.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Athlete;
use App\Models\Invoice;
use Carbon\Carbon;

/**
 * Comando para gerar faturas automáticas para atletas com planos ativos.
 */
class GenerateInvoices extends Command
{
    protected $signature = 'maisbase:generate-invoices';
    protected $description = 'Gera faturas mensais para atletas com planos de assinatura ativos';

    public function handle()
    {
        $this->info('Iniciando geração de faturas...');

        // Buscamos atletas ativos que possuem um plano vinculado
        $athletes = Athlete::where('status', 'ativo')
            ->whereNotNull('subscription_plan_id')
            ->get();

        $count = 0;

        foreach ($athletes as $athlete) {
            $dueDate = Carbon::now()->addDays(5); // Vencimento padrão em 5 dias

            // Verifica se já existe uma fatura pendente ou paga para o mês atual
            $exists = Invoice::where('athlete_id', $athlete->id)
                ->whereMonth('due_date', Carbon::now()->month)
                ->whereYear('due_date', Carbon::now()->year)
                ->exists();

            if (!$exists) {
                Invoice::create([
                    'tenant_id' => $athlete->tenant_id,
                    'athlete_id' => $athlete->id,
                    'amount' => $athlete->subscriptionPlan->amount,
                    'due_date' => $dueDate,
                    'status' => 'pending',
                ]);
                $count++;
            }
        }

        $this->info("Processo concluído. {$count} novas faturas geradas.");
    }
}
