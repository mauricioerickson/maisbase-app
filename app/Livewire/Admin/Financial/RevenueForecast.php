<?php

namespace App\Livewire\Admin\Financial;

use Livewire\Component;
use App\Models\Athlete;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;

class RevenueForecast extends Component
{
    public function render()
    {
        // Agrupamento de atletas por plano para cálculo de MRR
        $planForecasts = SubscriptionPlan::withCount(['athletes' => function($query) {
                $query->where('status', 'ativo');
            }])
            ->get()
            ->map(function($plan) {
                return [
                    'name' => $plan->name,
                    'amount' => $plan->amount,
                    'athletes_count' => $plan->athletes_count,
                    'total' => $plan->amount * $plan->athletes_count,
                    'due_day' => $plan->due_day
                ];
            });

        $totalMRR = $planForecasts->sum('total');
        $totalAthletes = $planForecasts->sum('athletes_count');

        return view('livewire.admin.financial.revenue-forecast', [
            'planForecasts' => $planForecasts,
            'totalMRR' => $totalMRR,
            'totalAthletes' => $totalAthletes,
        ])->layout('layouts.app');
    }
}
