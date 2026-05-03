<?php

// filepath: app/Livewire/Field/AttendanceSession.php

namespace App\Livewire\Field;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Athlete;
use App\Models\Attendance;
use Mary\Traits\Toast;
use Carbon\Carbon;

/**
 * Componente para o Professor realizar a chamada no campo.
 * Otimizado para Mobile e Compliance.
 */
class AttendanceSession extends Component
{
    use Toast;

    public $selected_schedule_id;
    public $date;
    public $attendances = []; // [athlete_id => ['present' => bool, 'justification' => string]]

    /**
     * Inicializa com o dia atual.
     */
    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $schedules = Schedule::with('category')->get();
        
        $athletes = [];
        if ($this->selected_schedule_id) {
            $schedule = Schedule::with([
                'enrollments.athlete.latestMedicalClearance',
                'enrollments.athlete.invoices' => fn ($query) => $query
                    ->whereIn('status', ['pending', 'overdue'])
                    ->where('due_date', '<', now()),
            ])->find($this->selected_schedule_id);
            $athletes = $schedule->enrollments->map(fn($e) => $e->athlete);

            // Carregar presenças já marcadas se existirem
            $existing = Attendance::where('schedule_id', $this->selected_schedule_id)
                ->where('date', $this->date)
                ->get()
                ->keyBy('athlete_id');

            foreach ($athletes as $athlete) {
                if (!isset($this->attendances[$athlete->id])) {
                    $this->attendances[$athlete->id] = [
                        'present' => $existing[$athlete->id]->is_present ?? false,
                        'justification' => $existing[$athlete->id]->justification ?? '',
                    ];
                }
            }
        }

        return view('livewire.field.attendance-session', [
            'schedules' => $schedules,
            'athletes' => $athletes
        ])->layout('layouts.app');
    }

    /**
     * Alterna a presença de um atleta com validação de compliance.
     */
    public function togglePresence($athleteId)
    {
        $athlete = Athlete::with([
            'latestMedicalClearance',
            'invoices' => fn ($query) => $query
                ->whereIn('status', ['pending', 'overdue'])
                ->where('due_date', '<', now()),
        ])->find($athleteId);
        $currentState = $this->attendances[$athleteId]['present'];

        // Se estiver tentando marcar presença (falso -> verdadeiro)
        if (!$currentState) {
            if (!$athlete->isCompliant()) {
                $this->warning("Atenção: {$athlete->name} possui pendências de saúde ou financeiras. Justificativa obrigatória.");
                // Não bloqueamos totalmente, mas sinalizamos a necessidade de atenção do professor.
            }
        }

        $this->attendances[$athleteId]['present'] = !$currentState;
        $this->saveAttendance($athleteId);
    }

    /**
     * Persiste a presença no banco de dados.
     */
    public function saveAttendance($athleteId)
    {
        Attendance::updateOrCreate(
            [
                'athlete_id' => $athleteId,
                'schedule_id' => $this->selected_schedule_id,
                'date' => $this->date,
            ],
            [
                'is_present' => $this->attendances[$athleteId]['present'],
                'justification' => $this->attendances[$athleteId]['justification'],
            ]
        );

        $this->success('Registro de presença atualizado.');
    }

    public function markAllPresent()
    {
        if (!$this->selected_schedule_id) {
            return;
        }

        $schedule = Schedule::with([
            'enrollments.athlete.latestMedicalClearance',
            'enrollments.athlete.invoices' => fn ($query) => $query
                ->whereIn('status', ['pending', 'overdue'])
                ->where('due_date', '<', now()),
        ])->find($this->selected_schedule_id);

        $rows = $schedule->enrollments
            ->map(function ($enrollment) {
                $athlete = $enrollment->athlete;

                $this->attendances[$athlete->id] = [
                    'present' => true,
                    'justification' => $this->attendances[$athlete->id]['justification'] ?? '',
                ];

                return [
                    'tenant_id' => $athlete->tenant_id,
                    'athlete_id' => $athlete->id,
                    'schedule_id' => $this->selected_schedule_id,
                    'date' => $this->date,
                    'is_present' => true,
                    'justification' => $this->attendances[$athlete->id]['justification'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->all();

        Attendance::upsert(
            $rows,
            ['athlete_id', 'schedule_id', 'date'],
            ['is_present', 'justification', 'updated_at']
        );

        $this->success('Todos os atletas foram marcados como presentes.');
    }
}
