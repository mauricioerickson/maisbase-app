<?php

// filepath: app/Models/AiNudgeLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

/**
 * Log de comunicações geradas por IA.
 */
class AiNudgeLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'athlete_id',
        'type',
        'message',
        'status',
        'recovered_amount',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
