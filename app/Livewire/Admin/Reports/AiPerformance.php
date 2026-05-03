<?php

// filepath: app/Livewire/Admin/Reports/AiPerformance.php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\AiNudgeLog;
use App\Models\Invoice;

class AiPerformance extends Component
{
    public function render()
    {
        // Métricas Gerais
        $totalSent = AiNudgeLog::count();
        $billingNudges = AiNudgeLog::where('type', 'like', 'billing%')->count();
        $retentionNudges = AiNudgeLog::where('type', 'retention')->count();

        // ROI: Mensalidades pagas após um nudge (Simulação simplificada para este MVP)
        $recoveredRevenue = AiNudgeLog::sum('recovered_amount');

        return view('livewire.admin.reports.ai-performance', [
            'totalSent' => $totalSent,
            'billingNudges' => $billingNudges,
            'retentionNudges' => $retentionNudges,
            'recoveredRevenue' => $recoveredRevenue,
        ])->layout('layouts.app');
    }
}
