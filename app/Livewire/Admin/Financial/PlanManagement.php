<?php

// filepath: app/Livewire/Admin/Financial/PlanManagement.php

namespace App\Livewire\Admin\Financial;

use Livewire\Component;
use App\Models\SubscriptionPlan;
use Mary\Traits\Toast;

class PlanManagement extends Component
{
    use Toast;

    public $name;
    public $amount;
    public $billing_cycle_days = 30;
    public $description;

    public bool $showDrawer = false;

    public function render()
    {
        $plans = SubscriptionPlan::all();

        return view('livewire.admin.financial.plan-management', [
            'plans' => $plans
        ])->layout('layouts.app');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'amount' => 'required|numeric|min:1',
            'billing_cycle_days' => 'required|numeric|min:1',
        ]);

        SubscriptionPlan::create([
            'name' => $this->name,
            'amount' => $this->amount,
            'billing_cycle_days' => $this->billing_cycle_days,
            'description' => $this->description,
        ]);

        $this->reset(['name', 'amount', 'billing_cycle_days', 'description', 'showDrawer']);
        $this->success('Plano criado com sucesso!');
    }

    public function delete($id)
    {
        SubscriptionPlan::findOrFail($id)->delete();
        $this->success('Plano removido.');
    }
}
