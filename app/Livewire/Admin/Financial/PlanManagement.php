<?php

// filepath: app/Livewire/Admin/Financial/PlanManagement.php

namespace App\Livewire\Admin\Financial;

use Livewire\Component;
use App\Models\SubscriptionPlan;
use Mary\Traits\Toast;

class PlanManagement extends Component
{
    use Toast;

    public $planId;
    public $name;
    public $amount;
    public $billing_cycle_days = 30;
    public $description;

    public bool $showDrawer = false;
    public bool $showDeleteModal = false;
    public $idToDelete;

    public function create()
    {
        $this->reset(['name', 'amount', 'billing_cycle_days', 'description', 'planId']);
        $this->showDrawer = true;
    }

    public function render()
    {
        $plans = SubscriptionPlan::all();

        return view('livewire.admin.financial.plan-management', [
            'plans' => $plans
        ])->layout('layouts.app');
    }

    public function edit($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->amount = $plan->amount;
        $this->billing_cycle_days = $plan->billing_cycle_days;
        $this->description = $plan->description;
        
        $this->showDrawer = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'amount' => 'required|numeric|min:1',
            'billing_cycle_days' => 'required|numeric|min:1',
        ]);

        try {
            SubscriptionPlan::updateOrCreate(
                ['id' => $this->planId],
                [
                    'name' => $this->name,
                    'amount' => $this->amount,
                    'billing_cycle_days' => $this->billing_cycle_days,
                    'description' => $this->description,
                ]
            );

            $message = $this->planId ? 'Plano atualizado com sucesso!' : 'Plano criado com sucesso!';
            
            $this->reset(['name', 'amount', 'billing_cycle_days', 'description', 'showDrawer', 'planId']);
            $this->success($message, redirectTo: route('financial.plans'));
        } catch (\Exception $e) {
            $this->error('Erro ao salvar o plano. Verifique os dados e tente novamente.');
        }
    }

    public function confirmDelete($id)
    {
        $this->idToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            SubscriptionPlan::findOrFail($this->idToDelete)->delete();
            $this->showDeleteModal = false;
            $this->success('Plano removido com sucesso!');
        } catch (\Exception $e) {
            $this->error('Não foi possível remover este plano.');
        }
    }
}
