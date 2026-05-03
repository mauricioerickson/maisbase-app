<?php

// filepath: app/Console/Commands/CalculateRiskScore.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Athlete;
use App\Models\Attendance;
use Carbon\Carbon;

/**
 * Motor de Inteligência de Retenção.
 * Calcula o risco de evasão (Churn) baseado na frequência recente.
 */
class CalculateRiskScore extends Command
{
    protected $signature = 'maisbase:calculate-risk';
    protected $description = 'Calcula o Risk Score dos atletas baseado em faltas consecutivas';

    public function handle()
    {
        $this->info('Iniciando análise de retenção...');

        $athletes = Athlete::where('status', 'ativo')->get();

        foreach ($athletes as $athlete) {
            // Pegamos as últimas 5 sessões de treino para as quais o atleta deveria estar presente
            $lastAttendances = Attendance::where('athlete_id', $athlete->id)
                ->orderBy('date', 'desc')
                ->take(5)
                ->get();

            $consecutiveAbsences = 0;
            foreach ($lastAttendances as $attendance) {
                if (!$attendance->is_present) {
                    $consecutiveAbsences++;
                } else {
                    break; // Paramos de contar na primeira presença encontrada
                }
            }

            // Lógica de Score:
            // 0 faltas: 0
            // 1 falta: 20
            // 2 faltas: 45 (Alerta Amarelo)
            // 3+ faltas: 90 (Alerta Vermelho - Churn iminente)
            $score = 0;
            if ($consecutiveAbsences == 1) $score = 20;
            elseif ($consecutiveAbsences == 2) $score = 45;
            elseif ($consecutiveAbsences >= 3) $score = 90;

            $athlete->update(['risk_score' => $score]);
        }

        $this->info('Risk Score atualizado para todos os atletas.');
    }
}
