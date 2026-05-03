<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->randomElement(['Plano Mensal', 'Plano Semestral', 'Elite Soccer']),
            'amount' => $this->faker->randomFloat(2, 100, 500),
            'billing_cycle_days' => 30,
            'description' => $this->faker->sentence(),
        ];
    }
}
