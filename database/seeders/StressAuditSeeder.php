<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\MedicalClearance;
use App\Models\Schedule;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StressAuditSeeder extends Seeder
{
    private const DAYS = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
    private const START_TIMES = ['07:00', '08:30', '10:00', '14:00', '15:30', '17:00', '18:30'];
    private const POSITIONS = ['Goleiro', 'Zagueiro', 'Lateral', 'Volante', 'Meia', 'Atacante'];

    public function run(): void
    {
        DB::transaction(function () {
            $this->resetTenant('arena-regional');
            $this->resetTenant('arena-interior');
            $this->resetTenant('elite-soccer-academy');

            $this->seedTenant([
                'name' => 'Arena Regional',
                'slug' => 'arena-regional',
                'admin_email' => 'admin@arena-regional.test',
                'athletes' => 80,
                'categories' => [
                    ['Sub-7', 5, 7],
                    ['Sub-9', 8, 9],
                    ['Sub-11', 10, 11],
                    ['Sub-13', 12, 13],
                ],
                'schedules' => 20,
                'amount' => 147.00,
            ]);

            $this->seedTenant([
                'name' => 'Elite Soccer Academy',
                'slug' => 'elite-soccer-academy',
                'admin_email' => 'admin@elite-soccer.test',
                'athletes' => 155,
                'categories' => [
                    ['Sub-7', 5, 7],
                    ['Sub-9', 8, 9],
                    ['Sub-11', 10, 11],
                    ['Sub-13', 12, 13],
                    ['Sub-17', 14, 17],
                    ['Feminino', 8, 17],
                ],
                'schedules' => 35,
                'amount' => 197.00,
            ]);
        });
    }

    private function resetTenant(string $slug): void
    {
        $tenant = Tenant::withTrashed()->where('slug', $slug)->first();

        if (!$tenant) {
            return;
        }

        $tenantId = $tenant->id;

        DB::table('ai_nudge_logs')->where('tenant_id', $tenantId)->delete();
        DB::table('medical_clearances')->where('tenant_id', $tenantId)->delete();
        DB::table('attendances')->where('tenant_id', $tenantId)->delete();
        DB::table('invoices')->where('tenant_id', $tenantId)->delete();
        DB::table('enrollments')->where('tenant_id', $tenantId)->delete();
        DB::table('athletes')->where('tenant_id', $tenantId)->delete();
        DB::table('schedules')->where('tenant_id', $tenantId)->delete();
        DB::table('categories')->where('tenant_id', $tenantId)->delete();
        DB::table('subscription_plans')->where('tenant_id', $tenantId)->delete();
        DB::table('guardians')->where('tenant_id', $tenantId)->delete();
        DB::table('users')->where('tenant_id', $tenantId)->delete();

        $tenant->forceDelete();
    }

    private function seedTenant(array $scenario): void
    {
        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'name' => $scenario['name'],
            'slug' => $scenario['slug'],
            'document' => fake()->numerify('##.###.###/####-##'),
            'pix_key' => $scenario['admin_email'],
            'nudge_tone' => 'amigavel',
            'active' => true,
        ]);

        session(['tenant_id' => $tenant->id]);

        User::create([
            'tenant_id' => $tenant->id,
            'name' => "{$scenario['name']} Admin",
            'email' => $scenario['admin_email'],
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $plan = SubscriptionPlan::create([
            'tenant_id' => $tenant->id,
            'name' => 'Plano Mensal',
            'amount' => $scenario['amount'],
            'billing_cycle_days' => 30,
            'description' => 'Plano de teste para auditoria de stress.',
        ]);

        $categories = collect($scenario['categories'])->map(fn (array $category) => Category::create([
            'tenant_id' => $tenant->id,
            'name' => $category[0],
            'min_age' => $category[1],
            'max_age' => $category[2],
        ]));

        $schedules = collect(range(1, $scenario['schedules']))->map(function (int $index) use ($categories, $tenant) {
            $start = self::START_TIMES[($index - 1) % count(self::START_TIMES)];

            return Schedule::create([
                'tenant_id' => $tenant->id,
                'category_id' => $categories[($index - 1) % $categories->count()]->id,
                'day_of_week' => self::DAYS[($index - 1) % count(self::DAYS)],
                'start_time' => $start,
                'end_time' => date('H:i', strtotime($start . ' +75 minutes')),
                'max_capacity' => 24,
            ]);
        });

        collect(range(1, $scenario['athletes']))->each(function (int $index) use ($scenario, $tenant, $plan, $schedules) {
            $guardian = Guardian::create([
                'tenant_id' => $tenant->id,
                'name' => "Responsavel {$scenario['name']} {$index}",
                'whatsapp_number' => '+5514' . str_pad((string) $index, 9, '0', STR_PAD_LEFT),
                'document' => fake()->numerify('###.###.###-##'),
            ]);

            $age = 6 + (($index - 1) % 12);

            $athlete = Athlete::create([
                'tenant_id' => $tenant->id,
                'guardian_id' => $guardian->id,
                'subscription_plan_id' => $plan->id,
                'name' => "Atleta {$scenario['name']} {$index}",
                'birth_date' => now()->subYears($age)->subDays($index % 365)->toDateString(),
                'position' => self::POSITIONS[($index - 1) % count(self::POSITIONS)],
                'status' => 'ativo',
                'risk_score' => $index % 11,
            ]);

            $primarySchedule = $schedules[($index - 1) % $schedules->count()];
            Enrollment::create([
                'tenant_id' => $tenant->id,
                'athlete_id' => $athlete->id,
                'schedule_id' => $primarySchedule->id,
            ]);

            Invoice::create([
                'tenant_id' => $tenant->id,
                'athlete_id' => $athlete->id,
                'amount' => $scenario['amount'],
                'due_date' => now()->addDays(($index % 20) - 5)->toDateString(),
                'status' => $index % 3 === 0 ? 'overdue' : 'pending',
                'external_id' => "{$scenario['slug']}-pix-{$index}",
                'pix_copy_paste' => '00020101021226850014br.gov.bcb.pix',
                'paid_at' => null,
            ]);

            MedicalClearance::create([
                'tenant_id' => $tenant->id,
                'athlete_id' => $athlete->id,
                'expiry_date' => now()->addDays($index % 9 === 0 ? -10 : 120)->toDateString(),
                'status' => $index % 9 === 0 ? 'expired' : 'valid',
            ]);
        });

        session()->forget('tenant_id');
    }
}
