<!-- filepath: resources/views/livewire/admin/financial/plan-management.blade.php -->
@section('page_title', 'Planos de Mensalidade')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-secondary font-system uppercase tracking-tight">Categorias Financeiras</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Defina os valores das mensalidades da sua Arena</p>
        </div>
        <x-mary-button label="Novo Plano" icon="o-plus" class="btn-primary btn-m3" wire:click="create" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="card-m3 p-8 bg-white border-t-8 border-primary flex flex-col h-full hover:shadow-xl transition-all group">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-xl font-bold text-secondary uppercase tracking-tighter">{{ $plan->name }}</h3>
                    <div class="flex gap-1">
                        <x-mary-button icon="o-pencil" class="btn-ghost btn-xs text-primary opacity-0 group-hover:opacity-100 transition-all" wire:click="edit({{ $plan->id }})" />
                        <x-mary-button icon="o-trash" class="btn-ghost btn-xs text-error opacity-0 group-hover:opacity-100 transition-all" wire:click="confirmDelete({{ $plan->id }})" />
                    </div>
                </div>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-secondary font-system">R$ {{ number_format($plan->amount, 2, ',', '.') }}</span>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">/ {{ $plan->billing_cycle_days }} dias</span>
                </div>

                <p class="text-slate-500 text-sm mb-8 flex-1 leading-relaxed">{{ $plan->description ?? 'Plano padrão de treinamento.' }}</p>

                <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold text-primary">
                        <span class="material-symbols-outlined text-sm">group</span>
                        {{ $plan->athletes()->count() }} Atletas
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Drawer de Cadastro --}}
    <x-mary-drawer wire:model="showDrawer" :title="$planId ? 'Editar Plano' : 'Configurar Novo Plano'" right class="w-full md:w-[400px]">
        <form wire:submit.prevent="save" id="plan-form">
            <div class="space-y-4">
                <x-mary-input label="Nome do Plano" wire:model="name" placeholder="Ex: Mensalidade Padrão" icon="o-tag" />
                
                <x-mary-input label="Valor (R$)" type="number" step="0.01" wire:model="amount" icon="o-currency-dollar" />
                
                <x-mary-input label="Ciclo de Cobrança (Dias)" type="number" wire:model="billing_cycle_days" icon="o-calendar" />
                
                <x-mary-textarea label="Descrição Curta" wire:model="description" placeholder="O que este plano inclui?" hint="Opcional" />
            </div>

            <x-slot:actions>
                <x-mary-button label="Cancelar" wire:click="$set('showDrawer', false)" />
                <x-mary-button label="{{ $planId ? 'Salvar Alterações' : 'Criar Plano' }}" icon="o-check" class="btn-primary" type="submit" spinner="save" form="plan-form" />
            </x-slot:actions>
        </form>
    </x-mary-drawer>

    {{-- Modal de Confirmação de Exclusão --}}
    <x-mary-modal wire:model="showDeleteModal" title="Confirmar Exclusão" class="backdrop-blur">
        <div class="mb-5">Tem certeza que deseja excluir este plano? Esta ação não pode ser desfeita e pode afetar atletas vinculados.</div>
        <x-slot:actions>
            <x-mary-button label="Cancelar" @click="$wire.showDeleteModal = false" />
            <x-mary-button label="Excluir" icon="o-trash" class="btn-error" wire:click="delete" spinner="delete" />
        </x-slot:actions>
    </x-mary-modal>
</div>
