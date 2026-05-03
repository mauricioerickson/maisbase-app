<?php

// filepath: app/Livewire/Admin/Financial/Dashboard.php

namespace App\Livewire\Admin\Financial;

use Livewire\Component;
use App\Models\Invoice;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Métricas do Mês
        $totalReceived = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalPending = Invoice::where('status', 'pending')
            ->where('due_date', '<=', $endOfMonth)
            ->sum('amount');

        $overdueCount = Invoice::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->count();

        // Dados para Gráfico Simples (Simulado para este MVP)
        $chartData = [
            'labels' => ['Recebido', 'Pendente'],
            'datasets' => [
                [
                    'label' => 'Financeiro (R$)',
                    'data' => [$totalReceived, $totalPending],
                    'backgroundColor' => ['#2E7D32', '#B00020'],
                ]
            ]
        ];

        return view('livewire.admin.financial.dashboard', [
            'totalReceived' => $totalReceived,
            'totalPending' => $totalPending,
            'overdueCount' => $overdueCount,
            'chartData' => $chartData,
        ])->layout('layouts.app');
    }
}
