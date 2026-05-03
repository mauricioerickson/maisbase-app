<?php

// filepath: app/Models/Guardian.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'whatsapp_number',
        'document',
    ];

    /**
     * Relacionamento com os atletas vinculados a este responsável.
     */
    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }
}
