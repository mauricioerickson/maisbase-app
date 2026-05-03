<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Guardian;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class AthleteFactory extends Factory
{
    protected $model = Athlete::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'guardian_id' => Guardian::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'name' => $this->faker->name('male'),
            'birth_date' => $this->faker->dateTimeBetween('-15 years', '-5 years')->format('Y-m-d'),
            'position' => $this->faker->randomElement(['Goleiro', 'Zagueiro', 'Lateral', 'Meio-campo', 'Atacante']),
            'status' => 'ativo',
            'risk_score' => $this->faker->numberBetween(0, 100),
        ];
    }
}
