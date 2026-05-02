<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('landing', [
        'cidade' => 'Sua Região',
        'is_geo' => false
    ]);
});

Route::get('/solucoes/{cidade}', function ($cidade) {
    // Basic formatting for city name (e.g. sao-paulo -> São Paulo)
    // You might want to use a proper helper/dictionary for this later
    $cityName = Str::title(str_replace('-', ' ', $cidade));
    return view('landing', ['cidade' => $cityName]);
})->name('landing');
