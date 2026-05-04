<?php

// filepath: app/Jobs/ProcessRetentionNudges.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Athlete;
use App\Models\AiNudgeLog;
use App\Services\AI\NudgeGenerator;
use App\Services\WhatsApp\WhatsAppService;

class ProcessRetentionNudges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NudgeGenerator $ai, WhatsAppService $wa)
    {
        // Atletas com alto risco de churn (score > 80) que ainda não receberam nudge de retenção este mês
        $atRisk = Athlete::where('risk_score', '>=', 80)
            ->where('status', 'ativo')
            ->with(['guardian', 'tenant'])
            ->get();

        foreach ($atRisk as $athlete) {
            $message = $ai->generate([
                'arena_name' => $athlete->tenant->name,
                'guardian_name' => $athlete->guardian->name,
                'athlete_name' => $athlete->name,
                'subject' => 'Reengajamento / Sentimos sua falta',
                'extra' => "O atleta faltou aos últimos treinos. Queremos entender se está tudo bem e incentivá-lo a voltar.",
                'tone' => $athlete->tenant->nudge_tone,
            ]);

            $wa->sendMessage($athlete->guardian->whatsapp_number, $message, $athlete->tenant_id);

            AiNudgeLog::create([
                'tenant_id' => $athlete->tenant_id,
                'athlete_id' => $athlete->id,
                'type' => 'retention',
                'message' => $message,
            ]);
        }
    }
}
