<?php

// filepath: app/Models/Attendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Attendance extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'athlete_id',
        'schedule_id',
        'date',
        'is_present',
        'justification',
    ];

    protected $casts = [
        'date' => 'date',
        'is_present' => 'boolean',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
