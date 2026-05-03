<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Schedule;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'category_id' => Category::factory(),
            'day_of_week' => $this->faker->randomElement(['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado']),
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
            'max_capacity' => 20,
        ];
    }
}
