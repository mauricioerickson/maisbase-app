<!-- filepath: resources/views/livewire/admin/staff-management.blade.php -->
@section('page_title', 'Gestão de Staff')

<div class="space-y-6">
    {{-- Header de Ações --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-secondary font-system uppercase tracking-tight">Equipe da Arena</h2>
        <x-mary-button label="Novo Membro" icon="o-plus" class="btn-primary btn-m3" @click="$wire.showDrawer = true" />
    </div>

    {{-- Tabela de Staff --}}
    <div class="card-m3 overflow-hidden">
        <x-mary-table :rows="$users" :headers="[
            ['key' => 'name', 'label' => 'Nome'],
            ['key' => 'email', 'label' => 'E-mail'],
            ['key' => 'role', 'label' => 'Cargo'],
            ['key' => 'created_at', 'label' => 'Membro desde'],
        ]">
            @scope('cell_role', $user)
                <x-mary-badge :label="strtoupper($user->role)" @class([
                    'bg-primary/10 text-primary border-none' => $user->role === 'admin',
                    'bg-accent/10 text-accent-content border-none' => $user->role === 'professor',
                    'bg-slate-100 text-slate-500 border-none' => $user->role === 'financeiro',
                ]) />
            @endscope

            @scope('cell_created_at', $user)
                <span class="text-xs text-slate-400">{{ $user->created_at->format('d/m/Y') }}</span>
            @endscope

            @scope('actions', $user)
                <div class="flex gap-2">
                    <x-mary-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="delete({{ $user->id }})" wire:confirm="Tem certeza?" />
                </div>
            @endscope
        </x-mary-table>
    </div>

    {{-- Drawer para Adicionar Membro --}}
    <x-mary-drawer wire:model="showDrawer" title="Adicionar Novo Membro" right class="w-full md:w-[400px]">
        <form wire:submit.prevent="save" class="space-y-4">
            <x-mary-input label="Nome Completo" wire:model="name" icon="o-user" required />
            <x-mary-input label="E-mail" type="email" wire:model="email" icon="o-envelope" required />
            <x-mary-select label="Cargo / Role" wire:model="role" icon="o-briefcase" :options="[
                ['id' => 'admin', 'name' => 'Administrador'],
                ['id' => 'professor', 'name' => 'Professor / Técnico'],
                ['id' => 'financeiro', 'name' => 'Financeiro'],
            ]" required />
            <x-mary-input label="Senha Inicial" type="password" wire:model="password" icon="o-key" required />
            
            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.showDrawer = false" />
                <x-mary-button label="Salvar Membro" icon="o-check" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </form>
    </x-mary-drawer>
</div>
