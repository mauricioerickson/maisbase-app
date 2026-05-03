<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendamento do Motor Financeiro MaisBase
Schedule::command('maisbase:generate-invoices')->dailyAt('03:00');
Schedule::command('maisbase:calculate-risk')->dailyAt('04:00');

// Disparos de Nudges IA (WhatsApp)
Schedule::job(new \App\Jobs\ProcessFinancialNudges)->dailyAt('09:00');
Schedule::job(new \App\Jobs\ProcessRetentionNudges)->dailyAt('09:30');
