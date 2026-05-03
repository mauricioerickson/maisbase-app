<?php

// filepath: app/Models/Invoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Invoice extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'athlete_id',
        'amount',
        'due_date',
        'status',
        'external_id',
        'pix_copy_paste',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
