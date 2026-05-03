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

$tenantId = 4; // Elite Soccer Academy
$tenant = Tenant::find($tenantId);

echo "Iniciando simulação para: " . $tenant->name . "\n";

// 1. Criar Categorias
$categories = [
    ['name' => 'Sub-15 Elite', 'min' => 14, 'max' => 15],
    ['name' => 'Sub-17 Elite', 'min' => 16, 'max' => 17],
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
echo "2 Categorias de Elite criadas.\n";

// 2. Grade de Horários Complexa (Manhã e Tarde)
$days = ['segunda', 'terca', 'quarta', 'quinta', 'sexta'];
$schedules = [];
foreach ($catModels as $name => $cat) {
    foreach ($days as $day) {
        // Manhã
        $schedules[$name . '_manha_' . $day] = Schedule::create([
            'tenant_id' => $tenantId,
            'category_id' => $cat->id,
            'day_of_week' => $day,
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'max_capacity' => 25,
        ]);
        // Tarde
        $schedules[$name . '_tarde_' . $day] = Schedule::create([
            'tenant_id' => $tenantId,
            'category_id' => $cat->id,
            'day_of_week' => $day,
            'start_time' => '15:00:00',
            'end_time' => '16:30:00',
            'max_capacity' => 25,
        ]);
    }
}
echo "Grade de horários (Manhã/Tarde) criada.\n";

// 3. Criar Planos
$planMensal = SubscriptionPlan::create([
    'tenant_id' => $tenantId,
    'name' => 'Elite Mensal',
    'amount' => 250.00,
    'billing_cycle_days' => 30,
]);

$planSemestral = SubscriptionPlan::create([
    'tenant_id' => $tenantId,
    'name' => 'Formação Semestral',
    'amount' => 1200.00,
    'billing_cycle_days' => 180,
]);
echo "Planos Elite criados.\n";

// 4. Cadastrar 150 Atletas
$faker = \Faker\Factory::create('pt_BR');
$scheduleList = array_values($schedules);

for ($i = 0; $i < 150; $i++) {
    $guardian = Guardian::create([
        'tenant_id' => $tenantId,
        'name' => $faker->name,
        'whatsapp_number' => $faker->phoneNumber,
        'document' => $faker->cpf,
    ]);

    $plan = ($i % 2 == 0) ? $planMensal : $planSemestral;
    $cat = ($i % 2 == 0) ? $catModels['Sub-15 Elite'] : $catModels['Sub-17 Elite'];
    
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

    // Matricular em 2 horários (ex: segunda e quarta)
    Enrollment::create([
        'tenant_id' => $tenantId,
        'athlete_id' => $athlete->id,
        'schedule_id' => $scheduleList[$i % count($scheduleList)]->id,
    ]);
}
echo "150 Atletas Elite cadastrados e matriculados.\n";
