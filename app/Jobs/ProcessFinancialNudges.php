<?php

// filepath: app/Jobs/ProcessFinancialNudges.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use App\Models\AiNudgeLog;
use App\Services\AI\NudgeGenerator;
use App\Services\WhatsApp\WhatsAppService;
use Carbon\Carbon;

class ProcessFinancialNudges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NudgeGenerator $ai, WhatsAppService $wa)
    {
        // 1. D-2: Lembrete de vencimento
        $upcoming = Invoice::where('status', 'pending')
            ->whereDate('due_date', Carbon::now()->addDays(2))
            ->with(['athlete.guardian', 'athlete.tenant'])
            ->get();

        foreach ($upcoming as $invoice) {
            $message = $ai->generate([
                'arena_name' => $invoice->athlete->tenant->name,
                'guardian_name' => $invoice->athlete->guardian->name,
                'athlete_name' => $invoice->athlete->name,
                'subject' => 'Lembrete de Mensalidade',
                'extra' => "Vencimento em 2 dias. Valor: R$ {$invoice->amount}. Chave PIX: {$invoice->athlete->tenant->pix_key}",
                'tone' => $invoice->athlete->tenant->nudge_tone,
            ]);

            $wa->sendMessage($invoice->athlete->guardian->whatsapp_number, $message);

            AiNudgeLog::create([
                'tenant_id' => $invoice->tenant_id,
                'athlete_id' => $invoice->athlete_id,
                'type' => 'billing_reminder',
                'message' => $message,
            ]);
        }

        // 2. D+1: Cobrança de atraso
        $overdue = Invoice::where('status', 'pending')
            ->whereDate('due_date', Carbon::now()->subDay())
            ->with(['athlete.guardian', 'athlete.tenant'])
            ->get();

        foreach ($overdue as $invoice) {
            $message = $ai->generate([
                'arena_name' => $invoice->athlete->tenant->name,
                'guardian_name' => $invoice->athlete->guardian->name,
                'athlete_name' => $invoice->athlete->name,
                'subject' => 'Mensalidade Atrasada',
                'extra' => "Venceu ontem. Valor: R$ {$invoice->amount}. Favor regularizar para evitar suspensão.",
                'tone' => $invoice->athlete->tenant->nudge_tone,
            ]);

            $wa->sendMessage($invoice->athlete->guardian->whatsapp_number, $message);

            AiNudgeLog::create([
                'tenant_id' => $invoice->tenant_id,
                'athlete_id' => $invoice->athlete_id,
                'type' => 'billing_overdue',
                'message' => $message,
            ]);
        }
    }
}
