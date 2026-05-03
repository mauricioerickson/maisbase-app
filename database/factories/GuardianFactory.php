<?php

namespace Database\Factories;

use App\Models\Guardian;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->name(),
            'whatsapp_number' => $this->faker->phoneNumber(),
            'document' => $this->faker->numerify('###########'),
        ];
    }
}
