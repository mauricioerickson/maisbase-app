<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'document',
        'pix_key',
        'nudge_tone',
        'active'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
