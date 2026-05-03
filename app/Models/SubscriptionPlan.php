<?php

// filepath: app/Models/SubscriptionPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SubscriptionPlan extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'amount',
        'billing_cycle_days',
        'description',
    ];

    public function athletes()
    {
        return $this->hasMany(Athlete::class, 'subscription_plan_id');
    }
}
