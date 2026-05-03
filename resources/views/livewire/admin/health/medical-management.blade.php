<!-- filepath: resources/views/livewire/admin/health/medical-management.blade.php -->
@section('page_title', 'Saúde e Compliance')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-secondary font-system uppercase tracking-tight">Controle de Atestados</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Garantindo a segurança dos atletas em campo</p>
        </div>
        <div class="w-full max-w-xs">
            <x-mary-input placeholder="Filtrar atleta..." wire:model.live="search" icon="o-magnifying-glass" clearable />
        </div>
    </div>

    <div class="card-m3 overflow-hidden">
        <x-mary-table :rows="$athletes" :headers="[
            ['key' => 'name', 'label' => 'Atleta'],
            ['key' => 'health_status', 'label' => 'Status Saúde'],
            ['key' => 'expiry_date', 'label' => 'Validade'],
        ]">
            @scope('cell_health_status', $athlete)
                @php
                    $clearance = $athlete->latestMedicalClearance;
                    $isExpired = $clearance ? $clearance->isExpired() : true;
                @endphp
                
                <x-mary-badge :label="$isExpired ? 'IRREGULAR' : 'APTO'" @class([
                    'bg-error text-white border-none' => $isExpired,
                    'bg-primary text-white border-none' => !$isExpired,
                ]) />
            @endscope

            @scope('cell_expiry_date', $athlete)
                <span @class([
                    'text-xs font-bold',
                    'text-error' => !$athlete->latestMedicalClearance || $athlete->latestMedicalClearance->isExpired(),
                    'text-slate-400' => $athlete->latestMedicalClearance && !$athlete->latestMedicalClearance->isExpired(),
                ])>
                    {{ $athlete->latestMedicalClearance ? $athlete->latestMedicalClearance->expiry_date->format('d/m/Y') : 'Nenhum' }}
                </span>
            @endscope

            @scope('actions', $athlete)
                <x-mary-button icon="o-arrow-up-tray" label="Upload" class="btn-ghost btn-sm text-primary" wire:click="openUpload({{ $athlete->id }})" />
            @endscope
        </x-mary-table>
    </div>

    <x-mary-drawer wire:model="showDrawer" title="Upload de Atestado" right class="w-full md:w-[400px]">
        <form wire:submit.prevent="save" class="space-y-6">
            <x-mary-input label="Data de Vencimento" type="date" wire:model="expiry_date" icon="o-calendar" required />
            
            <x-mary-file label="Foto ou PDF do Atestado" wire:model="file" accept="image/*,application/pdf" crop-after-change>
                <img src="/img/placeholder-doc.png" class="h-40 rounded-lg" />
            </x-mary-file>

            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.showDrawer = false" />
                <x-mary-button label="Salvar Atestado" icon="o-check" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </form>
    </x-mary-drawer>
</div>
