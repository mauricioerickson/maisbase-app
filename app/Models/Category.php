<?php

// filepath: app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

/**
 * Representa as categorias de idade (ex: Sub-11, Sub-13).
 */
class Category extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'min_age',
        'max_age',
    ];

    /**
     * Relacionamento com os horários vinculados a esta categoria.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
