<?php

// filepath: app/Models/Schedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

/**
 * Representa a grade de horários de treinos.
 */
class Schedule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'day_of_week',
        'start_time',
        'end_time',
        'max_capacity',
    ];

    /**
     * Relacionamento com a categoria.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relacionamento com as matrículas neste horário.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Verifica a ocupação atual.
     */
    public function getOccupancyAttribute()
    {
        return $this->enrollments()->count();
    }

    /**
     * Verifica se há vagas disponíveis.
     */
    public function hasVacancy()
    {
        return $this->occupancy < $this->max_capacity;
    }
}
