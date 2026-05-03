<?php

// filepath: app/Models/Enrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

/**
 * Representa o vínculo entre um Atleta e um Horário de Treino.
 */
class Enrollment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'athlete_id',
        'schedule_id',
        'technical_exception',
    ];

    /**
     * Relacionamento com o atleta.
     */
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Relacionamento com o horário.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
