<?php

use App\Models\Tenant;
use App\Models\Category;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Athlete;
use App\Models\Guardian;
use App\Models\SubscriptionPlan;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

$tenantId = 2; // Arena Interior
$tenant = Tenant::find($tenantId);

echo "Iniciando simulação para: " . $tenant->name . "\n";

// 1. Criar Categorias
$categories = [
    ['name' => 'Sub-7', 'min' => 5, 'max' => 7],
    ['name' => 'Sub-9', 'min' => 8, 'max' => 9],
    ['name' => 'Sub-11', 'min' => 10, 'max' => 11],
    ['name' => 'Sub-13', 'min' => 12, 'max' => 13],
];

$catModels = [];
foreach ($categories as $cat) {
    $catModels[$cat['name']] = Category::create([
        'tenant_id' => $tenantId,
        'name' => $cat['name'],
        'min_age' => $cat['min'],
        'max_age' => $cat['max'],
    ]);
}
echo "4 Categorias criadas.\n";

// 2. Criar Horários (Schedules)
$schedules = [];
foreach ($catModels as $name => $cat) {
    $schedules[$name] = Schedule::create([
        'tenant_id' => $tenantId,
        'category_id' => $cat->id,
        'day_of_week' => 'segunda',
        'start_time' => '08:00:00',
        'end_time' => '09:30:00',
        'max_capacity' => 20,
    ]);
}
echo "Horários criados.\n";

// 3. Criar Staff
User::create([
    'tenant_id' => $tenantId,
    'name' => 'Professor Bauru',
    'email' => 'professor@arenainterior.com.br',
    'password' => Hash::make('password123'),
    'role' => 'professor',
]);

User::create([
    'tenant_id' => $tenantId,
    'name' => 'Financeiro Bauru',
    'email' => 'financeiro@arenainterior.com.br',
    'password' => Hash::make('password123'),
    'role' => 'financeiro',
]);
echo "Staff criado.\n";

// 4. Criar Plano
$plan = SubscriptionPlan::create([
    'tenant_id' => $tenantId,
    'name' => 'Plano Escolinha',
    'amount' => 150.00,
    'billing_cycle_days' => 30,
]);

// 5. Cadastrar 80 Atletas
$faker = \Faker\Factory::create('pt_BR');
for ($i = 0; $i < 80; $i++) {
    $guardian = Guardian::create([
        'tenant_id' => $tenantId,
        'name' => $faker->name,
        'whatsapp_number' => $faker->phoneNumber,
        'document' => $faker->cpf,
    ]);

    $catName = array_keys($catModels)[$i % 4];
    $cat = $catModels[$catName];
    
    $birthDate = now()->subYears($faker->numberBetween($cat->min_age, $cat->max_age))->format('Y-m-d');

    $athlete = Athlete::create([
        'tenant_id' => $tenantId,
        'guardian_id' => $guardian->id,
        'subscription_plan_id' => $plan->id,
        'name' => $faker->name('male'),
        'birth_date' => $birthDate,
        'position' => $faker->randomElement(['Goleiro', 'Zagueiro', 'Lateral', 'Meia', 'Atacante']),
        'status' => 'ativo',
    ]);

    Enrollment::create([
        'tenant_id' => $tenantId,
        'athlete_id' => $athlete->id,
        'schedule_id' => $schedules[$catName]->id,
    ]);
}
echo "80 Atletas cadastrados e matriculados.\n";
