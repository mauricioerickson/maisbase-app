<?php

// filepath: routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * Rotas Públicas e Landing Page
 */
Route::get('/', function () {
    return redirect('/solucoes');
});

Route::get('/solucoes/{cidade?}', function ($cidade = null) {
    if (!$cidade) {
        $cityName = 'Sua Regiao';
        $displayCity = 'Sua Escola de Futebol';
    } else {
        // Expansão dinâmica de nomes de cidades e SEO Local
        $cityName = match ($cidade) {
            'rio-preto', 'sao-jose-do-rio-preto' => 'Sao Jose do Rio Preto',
            'bauru' => 'Bauru e Regiao',
            'rio', 'rio-de-janeiro' => 'Rio de Janeiro',
            default => Str::title(str_replace('-', ' ', $cidade)),
        };
        $displayCity = "Escolas de Futebol em {$cityName}";
    }

    $metaDescription = "MaisBase: O sistema de gestao numero 1 para {$displayCity}. Automeze chamadas, financeiro, PIX e reduza a inadimplencia com nossa IA de Nudges.";
    $ogTitle = "MaisBase - Gestao de Elite para Futebol em {$cityName}";

    return view('landing', [
        'cidade' => $cityName,
        'displayCity' => $displayCity,
        'metaDescription' => $metaDescription,
        'ogTitle' => $ogTitle,
        'canonicalUrl' => url('/solucoes/' . ($cidade ?: '')),
    ]);
})->name('landing');

/**
 * Rotas de Autenticação (Livewire)
 */
Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
Route::get('/register', \App\Livewire\Auth\RegisterArena::class)->name('register');

/**
 * Webhooks de Pagamento (Público)
 */
Route::post('/webhooks/payments/{gateway}', [\App\Http\Controllers\WebhookController::class, 'handle']);

Route::get('/logout', function () {
    Auth::logout();
    session()->forget('tenant_id');
    return redirect('/');
})->name('logout');

/**
 * Rotas Protegidas (Dashboard)
 */
Route::middleware(['auth', \App\Http\Middleware\IdentifyTenant::class])->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/atletas', \App\Livewire\Admin\Athletes\AthleteManagement::class)->name('athletes');
    Route::get('/admin/whatsapp', \App\Livewire\Admin\Config\WhatsAppConfig::class)->name('admin.whatsapp');
    Route::get('/academic/categorias', \App\Livewire\Admin\Academic\CategoryManagement::class)->name('academic.categories');
    Route::get('/campo/chamada', \App\Livewire\Field\AttendanceSession::class)->name('field.attendance');
    Route::get('/admin/saude', \App\Livewire\Admin\Health\MedicalManagement::class)->name('admin.health');
    Route::get('/admin/ia-performance', \App\Livewire\Admin\Reports\AiPerformance::class)->name('admin.ai');

    // Rotas Restritas: Apenas Admin e Financeiro
    Route::middleware('role:admin,financeiro')->group(function () {
        Route::get('/equipe', \App\Livewire\Admin\StaffManagement::class)->name('staff');
        Route::get('/financeiro/planos', \App\Livewire\Admin\Financial\PlanManagement::class)->name('financial.plans');
        Route::get('/financeiro/fluxo-caixa', \App\Livewire\Admin\Financial\Dashboard::class)->name('financial.dashboard');
        Route::get('/financeiro/previsao', \App\Livewire\Admin\Financial\RevenueForecast::class)->name('financial.forecast');
    });
});
