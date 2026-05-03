<?php

// filepath: app/Livewire/Admin/Athletes/AthleteManagement.php

namespace App\Livewire\Admin\Athletes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Athlete;
use App\Models\Guardian;
use App\Models\Schedule;
use App\Models\Enrollment;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;

class AthleteManagement extends Component
{
    use Toast, WithPagination;

    // Listagem
    public $search = '';

    // Form Atleta
    public $athlete_id;
    public $name;
    public $birth_date;
    public $position;
    public $status = 'ativo';
    public $guardian_id;

    // Form Responsável
    public $guardian_name;
    public $whatsapp_number;
    public $guardian_document;
    public bool $creatingNewGuardian = false;

    // Matrícula
    public $selected_athlete_for_enrollment;
    public $selected_schedule_id;
    public bool $technical_exception = false;

    public bool $showAthleteDrawer = false;
    public bool $showEnrollmentModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $athletes = Athlete::with(['guardian', 'schedules.category'])
            ->where('name', 'like', "%{$this->search}%")
            ->paginate(24);

        $guardians = Guardian::all();
        $schedules = Schedule::with('category')->withCount('enrollments')->get();

        return view('livewire.admin.athletes.athlete-management', [
            'athletes' => $athletes,
            'guardians' => $guardians,
            'schedules' => $schedules,
        ])->layout('layouts.app');
    }

    public function saveAthlete()
    {
        $this->validate([
            'name' => 'required|min:3',
            'birth_date' => 'required|date',
            'position' => 'nullable',
            'guardian_id' => $this->creatingNewGuardian ? 'nullable' : 'required|exists:guardians,id',
            'guardian_name' => $this->creatingNewGuardian ? 'required|min:3' : 'nullable',
            'whatsapp_number' => $this->creatingNewGuardian ? 'required|min:10' : 'nullable',
        ]);

        DB::transaction(function () {
            // Se estiver criando novo responsável
            if ($this->creatingNewGuardian) {
                $guardian = Guardian::create([
                    'name' => $this->guardian_name,
                    'whatsapp_number' => $this->whatsapp_number,
                    'document' => $this->guardian_document,
                ]);
                $this->guardian_id = $guardian->id;
            }

            Athlete::updateOrCreate(
                ['id' => $this->athlete_id],
                [
                    'name' => $this->name,
                    'birth_date' => $this->birth_date,
                    'position' => $this->position,
                    'status' => $this->status,
                    'guardian_id' => $this->guardian_id,
                ]
            );
        });

        $this->reset(['athlete_id', 'name', 'birth_date', 'position', 'status', 'guardian_id', 'guardian_name', 'whatsapp_number', 'guardian_document', 'creatingNewGuardian', 'showAthleteDrawer']);
        $this->success('Atleta salvo com sucesso!');
    }

    public function openEnrollment($athleteId)
    {
        $this->selected_athlete_for_enrollment = Athlete::find($athleteId);
        $this->showEnrollmentModal = true;
    }

    public function enroll()
    {
        $this->validate([
            'selected_schedule_id' => 'required|exists:schedules,id',
        ]);

        $schedule = Schedule::find($this->selected_schedule_id);
        $athlete = $this->selected_athlete_for_enrollment;

        // Validação de Vagas
        if (!$schedule->hasVacancy()) {
            $this->error('Esta turma atingiu a capacidade máxima!');
            return;
        }

        // Validação de Idade
        $age = $athlete->age;
        $min = $schedule->category->min_age;
        $max = $schedule->category->max_age;

        if (($min && $age < $min) || ($max && $age > $max)) {
            if (!$this->technical_exception) {
                $this->warning('Idade incompatível com a categoria. Marque "Exceção Técnica" para prosseguir.');
                return;
            }
        }

        Enrollment::create([
            'athlete_id' => $athlete->id,
            'schedule_id' => $schedule->id,
            'technical_exception' => $this->technical_exception,
        ]);

        $this->reset(['selected_schedule_id', 'technical_exception', 'showEnrollmentModal']);
        $this->success('Matrícula realizada com sucesso!');
    }
}
