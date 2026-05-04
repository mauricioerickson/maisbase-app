<?php

// filepath: app/Livewire/Admin/Dashboard.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Athlete;
use App\Models\Invoice;

use Barryvdh\DomPDF\Facade\Pdf;

class Dashboard extends Component
{
    use \Mary\Traits\Toast;

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalAthletes' => Athlete::count(),
            'totalRevenue' => Invoice::where('status', 'paid')->sum('amount'),
            'pendingInvoices' => Invoice::where('status', 'pending')->count(),
            'atRiskAthletes' => Athlete::where('risk_score', '>', 0)->orderBy('risk_score', 'desc')->take(5)->get(),
        ])->layout('layouts.app');
    }

    /**
     * Gera e baixa o relatório completo de retenção e risco de evasão (Churn).
     */
    public function viewFullReport()
    {
        $atRiskAthletes = Athlete::with(['guardian', 'attendances'])
            ->where('risk_score', '>', 0)
            ->orderBy('risk_score', 'desc')
            ->get();

        $data = [
            'totalAthletes' => Athlete::count(),
            'atRiskCount' => $atRiskAthletes->count(),
            'atRiskAthletes' => $atRiskAthletes,
        ];

        $pdf = Pdf::loadView('reports.retention-pdf', $data);

        $fileName = 'relatorio-retencao-evasao-' . now()->format('Y-m-d') . '.pdf';

        $this->success('Relatório de Retenção gerado com sucesso!');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }
}
