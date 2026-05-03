<?php

// filepath: app/Models/MedicalClearance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class MedicalClearance extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'athlete_id',
        'expiry_date',
        'file_path',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Verifica se o atestado está vencido.
     */
    public function isExpired()
    {
        return $this->expiry_date->isPast();
    }
}
