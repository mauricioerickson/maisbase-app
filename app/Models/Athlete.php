<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'guardian_id',
        'name',
        'birth_date',
        'position',
        'status',
        'risk_score',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Calcula a idade do atleta.
     */
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'enrollments');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function medicalClearances()
    {
        return $this->hasMany(MedicalClearance::class);
    }

    public function latestMedicalClearance()
    {
        return $this->hasOne(MedicalClearance::class)->latestOfMany();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Verifica se o atleta está apto para treino (Saúde e Financeiro).
     */
    public function isCompliant()
    {
        $hasValidMedical = $this->latestMedicalClearance && !$this->latestMedicalClearance->isExpired();
        $hasNoPendingInvoices = !$this->invoices()->where('status', 'pending')->where('due_date', '<', now())->exists();

        return $hasValidMedical && $hasNoPendingInvoices;
    }
}
