<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => $this->faker->company(),
            'slug' => $this->faker->unique()->slug(),
            'whatsapp' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'active' => true,
        ];
    }
}
