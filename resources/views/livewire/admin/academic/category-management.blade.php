<!-- filepath: resources/views/livewire/admin/academic/category-management.blade.php -->
@section('page_title', 'Categorias e Grades')

<div class="space-y-8">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-secondary font-system uppercase tracking-tight">Estrutura Acadêmica</h2>
            <p class="text-sm text-slate-400 font-medium">Gerencie faixas etárias e horários de treinos</p>
        </div>
        <x-mary-button label="Nova Categoria" icon="o-plus" class="btn-primary btn-m3" @click="$wire.showCategoryDrawer = true" />
    </div>

    {{-- Grid de Categorias --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($categories as $category)
            <div class="card-m3 p-6 flex flex-col h-full bg-white border-l-8 border-primary">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-secondary uppercase tracking-tight">{{ $category->name }}</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                            Público: {{ $category->min_age ?? '0' }} a {{ $category->max_age ?? '99' }} anos
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <x-mary-button icon="o-calendar-days" class="btn-ghost btn-sm text-primary" wire:click="openScheduleDrawer({{ $category->id }})" />
                        <x-mary-button icon="o-trash" class="btn-ghost btn-sm text-error" wire:click="deleteCategory({{ $category->id }})" wire:confirm="Isso excluirá todos os horários vinculados. Confirmar?" />
                    </div>
                </div>

                {{-- Tabela de Horários na Categoria --}}
                <div class="space-y-3 flex-1">
                    @forelse($category->schedules as $schedule)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg group hover:bg-slate-100 transition-all border border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                <div>
                                    <p class="text-xs font-bold text-secondary uppercase">{{ $schedule->day_of_week }}</p>
                                    <p class="text-[10px] text-slate-500 font-medium">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Ocupação</p>
                                    <p class="text-xs font-bold @if($schedule->occupancy >= $schedule->max_capacity) text-error @else text-primary @endif">
                                        {{ $schedule->occupancy }}/{{ $schedule->max_capacity }}
                                    </p>
                                </div>
                                <x-mary-button icon="o-x-mark" class="btn-ghost btn-xs text-error opacity-0 group-hover:opacity-100 transition-all" wire:click="deleteSchedule({{ $schedule->id }})" />
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 border-2 border-dashed border-slate-100 rounded-xl">
                            <p class="text-xs text-slate-400 font-bold uppercase italic">Nenhum horário definido</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Drawers --}}
    <x-mary-drawer wire:model="showCategoryDrawer" title="Nova Categoria" right class="w-full md:w-[400px]">
        <form wire:submit.prevent="saveCategory" class="space-y-4">
            <x-mary-input label="Nome (ex: Sub-13)" wire:model="name" icon="o-tag" required />
            <div class="grid grid-cols-2 gap-4">
                <x-mary-input label="Idade Mínima" type="number" wire:model="min_age" icon="o-user" />
                <x-mary-input label="Idade Máxima" type="number" wire:model="max_age" icon="o-user" />
            </div>
            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.showCategoryDrawer = false" />
                <x-mary-button label="Criar Categoria" icon="o-check" class="btn-primary" type="submit" spinner="saveCategory" />
            </x-slot:actions>
        </form>
    </x-mary-drawer>

    <x-mary-drawer wire:model="showScheduleDrawer" title="Adicionar Horário" right class="w-full md:w-[400px]">
        <form wire:submit.prevent="saveSchedule" class="space-y-4">
            <x-mary-select label="Dia da Semana" wire:model="day_of_week" icon="o-calendar" :options="[
                ['id' => 'segunda', 'name' => 'Segunda-feira'],
                ['id' => 'terca', 'name' => 'Terça-feira'],
                ['id' => 'quarta', 'name' => 'Quarta-feira'],
                ['id' => 'quinta', 'name' => 'Quinta-feira'],
                ['id' => 'sexta', 'name' => 'Sexta-feira'],
                ['id' => 'sabado', 'name' => 'Sábado'],
                ['id' => 'domingo', 'name' => 'Domingo'],
            ]" required />
            
            <div class="grid grid-cols-2 gap-4">
                <x-mary-input label="Início" type="time" wire:model="start_time" icon="o-clock" required />
                <x-mary-input label="Fim" type="time" wire:model="end_time" icon="o-clock" required />
            </div>

            <x-mary-input label="Capacidade Máxima de Atletas" type="number" wire:model="max_capacity" icon="o-users" required />

            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.showScheduleDrawer = false" />
                <x-mary-button label="Salvar Horário" icon="o-check" class="btn-primary" type="submit" spinner="saveSchedule" />
            </x-slot:actions>
        </form>
    </x-mary-drawer>
</div>
