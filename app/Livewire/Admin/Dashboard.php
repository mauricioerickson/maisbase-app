<?php

// filepath: app/Livewire/Admin/Dashboard.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Athlete;
use App\Models\Invoice;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalAthletes' => Athlete::count(),
            'totalRevenue' => Invoice::where('status', 'paid')->sum('amount'),
            'pendingInvoices' => Invoice::where('status', 'pending')->count(),
            'atRiskAthletes' => Athlete::where('risk_score', '>', 0)->orderBy('risk_score', 'desc')->take(5)->get(),
        ])->layout('layouts.app');
    }
}
