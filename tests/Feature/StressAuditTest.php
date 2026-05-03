<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\WebhookLog;
use App\Services\AI\NudgeGenerator;
use Database\Seeders\StressAuditSeeder;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StressAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_tenants_are_isolated_by_global_scope(): void
    {
        $this->seed(StressAuditSeeder::class);

        $arenaRegional = Tenant::where('slug', 'arena-regional')->firstOrFail();
        $eliteSoccer = Tenant::where('slug', 'elite-soccer-academy')->firstOrFail();

        session(['tenant_id' => $arenaRegional->id]);
        $this->assertSame(80, Athlete::count());
        $this->assertFalse(Athlete::pluck('name')->contains(fn (string $name) => str_contains($name, 'Elite Soccer Academy')));

        session(['tenant_id' => $eliteSoccer->id]);
        $this->assertSame(155, Athlete::count());
        $this->assertFalse(Athlete::pluck('name')->contains(fn (string $name) => str_contains($name, 'Arena Interior')));

        session()->forget('tenant_id');
        $this->assertSame(235, Athlete::withoutGlobalScope('tenant')->count());
    }

    public function test_webhook_marks_one_hundred_pix_invoices_as_paid_without_loss(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        config(['services.webhooks.payments_secret' => 'test-secret']);
        $this->seed(StressAuditSeeder::class);

        $externalIds = Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', Tenant::where('slug', 'elite-soccer-academy')->value('id'))
            ->where('status', 'pending')
            ->limit(100)
            ->pluck('external_id');

        $this->assertCount(100, $externalIds);

        $externalIds->each(function (string $externalId) {
            $payload = [
                'event' => 'PAYMENT_RECEIVED',
                'payment' => ['id' => $externalId],
            ];

            $this->postJson('/webhooks/payments/asaas', $payload, $this->signedHeaders($payload))
                ->assertOk()
                ->assertJson(['status' => 'success']);
        });

        $this->assertSame(
            100,
            Invoice::withoutGlobalScope('tenant')->whereIn('external_id', $externalIds)->where('status', 'paid')->count()
        );
        $this->assertSame(100, WebhookLog::count());

        $payload = [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => ['id' => $externalIds->first()],
        ];

        $this->postJson('/webhooks/payments/asaas', $payload, $this->signedHeaders($payload))
            ->assertOk()
            ->assertJson(['message' => 'Pagamento ja confirmado']);

        $this->assertSame(101, WebhookLog::count());
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        config(['services.webhooks.payments_secret' => 'test-secret']);

        $this->postJson('/webhooks/payments/asaas', [
            'event' => 'PAYMENT_RECEIVED',
            'payment' => ['id' => 'pix-1'],
        ], ['X-Webhook-Signature' => 'invalid'])->assertUnauthorized();
    }

    public function test_ai_fallback_includes_required_dynamic_context(): void
    {
        config(['services.gemini.key' => null]);

        $message = app(NudgeGenerator::class)->generate([
            'arena_name' => 'Arena Rio Preto',
            'guardian_name' => 'Mariana',
            'athlete_name' => 'Joao',
            'subject' => 'mensalidade com vencimento em 10/05/2026',
            'extra' => 'PIX disponivel no portal',
            'tone' => 'amigavel',
        ]);

        $this->assertStringContainsString('Joao', $message);
        $this->assertStringContainsString('10/05/2026', $message);
        $this->assertStringContainsString('Sao Jose do Rio Preto', $message);
    }

    public function test_local_landing_pages_have_city_h1_and_geo_description(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $landing = file_get_contents(resource_path('views/landing.blade.php'));

        $this->assertStringContainsString('sao-jose-do-rio-preto', $routes);
        $this->assertStringContainsString('Sao Jose do Rio Preto', $routes);
        $this->assertStringContainsString('meta name="description"', $landing);
        $this->assertStringContainsString('og:description', $landing);
        $this->assertStringContainsString('canonical', $landing);
    }

    public function test_complex_scale_queries_join_athletes_plans_and_attendance_under_one_hundred_ms(): void
    {
        $this->seed(StressAuditSeeder::class);

        $tenant = Tenant::where('slug', 'elite-soccer-academy')->firstOrFail();
        session(['tenant_id' => $tenant->id]);

        $start = microtime(true);

        $athletes = Athlete::query()
            ->with(['guardian', 'subscriptionPlan', 'attendances'])
            ->whereHas('subscriptionPlan')
            ->limit(155)
            ->get();

        $elapsedMs = (microtime(true) - $start) * 1000;

        $this->assertSame(155, $athletes->count());
        $this->assertLessThan(100, $elapsedMs);
        $this->assertSame(0, Attendance::whereHas('athlete', fn ($query) => $query->where('name', 'like', '%Arena Regional%'))->count());
    }

    private function signedHeaders(array $payload): array
    {
        return [
            'X-Webhook-Signature' => hash_hmac('sha256', json_encode($payload), 'test-secret'),
        ];
    }
}
