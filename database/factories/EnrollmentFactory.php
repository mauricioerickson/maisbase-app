<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'athlete_id' => Athlete::factory(),
            'schedule_id' => Schedule::factory(),
            'technical_exception' => false,
        ];
    }
}
