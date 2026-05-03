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
        $cityName = 'Sua Região';
    } else {
        // Formatação simples para o nome da cidade
        $cityName = Str::title(str_replace('-', ' ', $cidade));
    }
    return view('landing', ['cidade' => $cityName]);
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
    Route::get('/equipe', \App\Livewire\Admin\StaffManagement::class)->name('staff');
    Route::get('/academic/categorias', \App\Livewire\Admin\Academic\CategoryManagement::class)->name('academic.categories');
    Route::get('/atletas', \App\Livewire\Admin\Athletes\AthleteManagement::class)->name('athletes');
    Route::get('/financeiro/planos', \App\Livewire\Admin\Financial\PlanManagement::class)->name('financial.plans');
    Route::get('/financeiro/fluxo-caixa', \App\Livewire\Admin\Financial\Dashboard::class)->name('financial.dashboard');
    Route::get('/campo/chamada', \App\Livewire\Field\AttendanceSession::class)->name('field.attendance');
    Route::get('/admin/saude', \App\Livewire\Admin\Health\MedicalManagement::class)->name('admin.health');
    Route::get('/admin/ia-performance', \App\Livewire\Admin\Reports\AiPerformance::class)->name('admin.ai');
    
    // Outras rotas administrativas virão aqui
});
