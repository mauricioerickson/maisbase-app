<?php

// filepath: app/Livewire/Admin/Health/MedicalManagement.php

namespace App\Livewire\Admin\Health;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Athlete;
use App\Models\MedicalClearance;
use Mary\Traits\Toast;

/**
 * Gestão de Atestados Médicos e Compliance de Saúde.
 */
class MedicalManagement extends Component
{
    use Toast, WithFileUploads;

    public $search = '';
    public $selected_athlete_id;
    public $expiry_date;
    public $file;

    public bool $showDrawer = false;

    public function render()
    {
        $athletes = Athlete::with('latestMedicalClearance')
            ->where('name', 'like', "%{$this->search}%")
            ->get();

        return view('livewire.admin.health.medical-management', [
            'athletes' => $athletes
        ])->layout('layouts.app');
    }

    public function openUpload($athleteId)
    {
        $this->selected_athlete_id = $athleteId;
        $this->showDrawer = true;
    }

    public function save()
    {
        $this->validate([
            'expiry_date' => 'required|date|after:today',
            'file' => 'required|image|max:2048', // Aceita foto do atestado
        ]);

        $path = $this->file->store('medical_clearances', 'public');

        MedicalClearance::create([
            'athlete_id' => $this->selected_athlete_id,
            'expiry_date' => $this->expiry_date,
            'file_path' => $path,
            'status' => 'valid',
        ]);

        $this->reset(['selected_athlete_id', 'expiry_date', 'file', 'showDrawer']);
        $this->success('Atestado registrado com sucesso!');
    }
}
