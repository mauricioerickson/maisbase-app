<!-- filepath: resources/views/livewire/admin/athletes/athlete-management.blade.php -->
@section('page_title', 'Gestão de Atletas')

<div class="space-y-8 relative pb-20">
    {{-- Header & Search --}}
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex-1 w-full max-w-md">
                <x-mary-input placeholder="Buscar atleta pelo nome..." wire:model.live.debounce.300ms="search" icon="o-magnifying-glass" clearable />
            </div>
            <div class="hidden md:block">
                <x-mary-button label="Novo Atleta" icon="o-plus" class="btn-primary btn-m3" wire:click="create" />
            </div>
        </div>

        {{-- Filtros Avançados --}}
        <div class="card-m3 p-4 bg-white shadow-sm border border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <x-mary-select label="Categoria" wire:model.live="filter_category_id" icon="o-academic-cap" 
                    :options="$categories" placeholder="Todas as categorias" clearable />
                
                <x-mary-select label="Dia da Aula" wire:model.live="filter_day" icon="o-calendar" 
                    :options="[
                        ['id' => 'segunda', 'name' => 'Segunda'],
                        ['id' => 'terca', 'name' => 'Terça'],
                        ['id' => 'quarta', 'name' => 'Quarta'],
                        ['id' => 'quinta', 'name' => 'Quinta'],
                        ['id' => 'sexta', 'name' => 'Sexta'],
                        ['id' => 'sabado', 'name' => 'Sábado'],
                        ['id' => 'domingo', 'name' => 'Domingo'],
                    ]" placeholder="Todos os dias" clearable />

                <x-mary-input label="Idade Específica" type="number" wire:model.live="filter_age" icon="o-user" placeholder="Ex: 12" clearable />

                <div class="flex gap-2">
                    <x-mary-button icon="o-x-mark" class="btn-ghost text-slate-400" title="Limpar Filtros" @click="$wire.set('filter_category_id', null); $wire.set('filter_day', null); $wire.set('filter_age', null); $wire.set('search', '')" />
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">
                        Resultados: <br>
                        <span class="text-primary text-sm">{{ $athletes->total() }} Atletas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        {{ $athletes->links() }}
    </div>

    {{-- Grid de Atletas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($athletes as $athlete)
            <div class="card-m3 p-6 bg-white hover:shadow-xl transition-all group border-t-4 @if($athlete->status === 'ativo') border-primary @else border-slate-300 @endif">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-secondary font-bold text-lg">
                        {{ substr($athlete->name, 0, 1) }}
                    </div>
                    <x-mary-badge :label="strtoupper($athlete->status)" @class([
                        'bg-primary/10 text-primary border-none text-[10px]' => $athlete->status === 'ativo',
                        'bg-slate-100 text-slate-500 border-none text-[10px]' => $athlete->status !== 'ativo',
                    ]) />
                </div>

                <h3 class="font-bold text-secondary text-lg truncate mb-1">{{ $athlete->name }}</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-4">{{ $athlete->position ?? 'Sem Posição' }} • {{ $athlete->age }} Anos</p>

                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-sm">person</span>
                        {{ $athlete->guardian->name }}
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-primary uppercase tracking-tighter">
                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                        {{ count($athlete->schedules) }} Horários Ativos
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-slate-50">
                    <x-mary-button label="Matricular" icon="o-academic-cap" class="btn-ghost btn-sm text-primary flex-1" wire:click="openEnrollment({{ $athlete->id }})" />
                    <x-mary-button icon="o-pencil" class="btn-ghost btn-sm text-slate-400" wire:click="edit({{ $athlete->id }})" />
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center card-m3 bg-slate-50 border-2 border-dashed border-slate-200">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-4">person_off</span>
                <p class="text-slate-400 font-bold uppercase">Nenhum atleta encontrado</p>
            </div>
        @endforelse
    </div>

    {{-- FAB Mobile --}}
    <div class="fixed bottom-24 right-6 md:hidden">
        <x-mary-button icon="o-plus" class="btn-primary rounded-full w-16 h-16 shadow-2xl" wire:click="create" />
    </div>

    {{-- Drawer Cadastro Atleta --}}
    <x-mary-drawer wire:model="showAthleteDrawer" :title="$athlete_id ? 'Editar Ficha do Atleta' : 'Novo Atleta'" right class="w-full md:w-[500px]">
        <form wire:submit.prevent="saveAthlete" class="space-y-6" id="athlete-form">
            {{-- Seção Atleta --}}
            <div class="space-y-4">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Informações Esportivas</div>
                <x-mary-input label="Nome Completo" wire:model="name" icon="o-user" required />
                <x-mary-select label="Plano de Mensalidade" wire:model="subscription_plan_id" icon="o-credit-card" :options="$plans" placeholder="Selecione um plano..." required />
                <div class="grid grid-cols-2 gap-4">
                    <x-mary-input label="Nascimento" type="date" wire:model="birth_date" icon="o-calendar" required />
                    <x-mary-select label="Posição" wire:model="position" icon="o-map-pin" :options="[
                        ['id' => 'Goleiro', 'name' => 'Goleiro'],
                        ['id' => 'Zagueiro', 'name' => 'Zagueiro'],
                        ['id' => 'Lateral', 'name' => 'Lateral'],
                        ['id' => 'Meia', 'name' => 'Meia'],
                        ['id' => 'Atacante', 'name' => 'Atacante'],
                    ]" />
                </div>
            </div>

            {{-- Seção Responsável --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Responsável Legal</div>
                    <x-mary-checkbox label="Novo?" wire:model.live="creatingNewGuardian" tight />
                </div>

                @if($creatingNewGuardian)
                    <x-mary-input label="Nome do Responsável" wire:model="guardian_name" icon="o-user" required />
                    <x-mary-input label="WhatsApp" wire:model="whatsapp_number" icon="o-phone" placeholder="+55..." required />
                    <x-mary-input label="CPF/Documento" wire:model="guardian_document" icon="o-identification" />
                @else
                    <x-mary-select label="Selecionar Responsável" wire:model="guardian_id" icon="o-users" :options="$guardians" required />
                @endif
            </div>

            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.showAthleteDrawer = false" />
                <x-mary-button label="{{ $athlete_id ? 'Salvar Alterações' : 'Salvar Atleta' }}" icon="o-check" class="btn-primary" type="submit" spinner="saveAthlete" form="athlete-form" />
            </x-slot:actions>
        </form>
    </x-mary-drawer>

    {{-- Modal de Matrícula --}}
    <x-mary-modal wire:model="showEnrollmentModal" title="Matrícula em Turma" class="bg-white">
        @if($selected_athlete_for_enrollment)
            <div class="mb-6 p-4 bg-primary/5 rounded-lg border border-primary/10">
                <p class="text-xs font-bold text-primary uppercase">Atleta</p>
                <p class="font-bold text-secondary">{{ $selected_athlete_for_enrollment->name }} ({{ $selected_athlete_for_enrollment->age }} anos)</p>
            </div>

            <form wire:submit.prevent="enroll" class="space-y-4" id="enrollment-form">
                <x-mary-select label="Selecione o Horário" wire:model.live="selected_schedule_id" icon="o-calendar-days" 
                    :options="$schedules->map(fn($s) => ['id' => $s->id, 'name' => $s->category->name . ' - ' . strtoupper($s->day_of_week) . ' (' . $s->enrollments_count . '/' . $s->max_capacity . ')'])" 
                    placeholder="Escolha uma turma..." required />

                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200 space-y-3">
                    <div class="flex items-center gap-2 text-amber-700">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        <span class="text-xs font-bold uppercase">Restrições Técnicas</span>
                    </div>
                    <x-mary-checkbox label="Autorizar exceção de idade/categoria" wire:model="technical_exception" class="checkbox-warning" />
                </div>

                <x-slot:actions>
                    <x-mary-button label="Cancelar" @click="$wire.showEnrollmentModal = false" />
                    <x-mary-button label="Finalizar Matrícula" icon="o-check" class="btn-primary" type="submit" spinner="enroll" form="enrollment-form" />
                </x-slot:actions>
            </form>
        @endif
    </x-mary-modal>
</div>
