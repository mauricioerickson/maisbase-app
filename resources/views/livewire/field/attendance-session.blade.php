<!-- filepath: resources/views/livewire/field/attendance-session.blade.php -->
@section('page_title', 'Chamada no Campo')

<div class="space-y-6 pb-20">
    {{-- Seletor de Grade --}}
    <div class="card-m3 p-6 bg-white shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-mary-select label="Selecione a Turma" wire:model.live="selected_schedule_id" icon="o-academic-cap" 
                :options="$schedules->map(fn($s) => ['id' => $s->id, 'name' => $s->category->name . ' - ' . strtoupper($s->day_of_week)])" 
                placeholder="Escolha o horário do treino..." />
            
            <x-mary-input label="Data do Treino" type="date" wire:model.live="date" icon="o-calendar" />
        </div>
    </div>

    @if($selected_schedule_id)
        {{-- Lista de Atletas para Chamada --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 px-2">
                <div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-[0.2em]">Lista de Atletas</h3>
                    <span class="text-[10px] font-bold text-primary uppercase">{{ count($athletes) }} Alunos Inscritos</span>
                </div>
                <x-mary-button label="Marcar todos presentes" icon="o-check-circle" class="btn-primary btn-sm min-h-12" wire:click="markAllPresent" spinner="markAllPresent" />
            </div>

            <div class="grid grid-cols-1 gap-3">
                @foreach($athletes as $athlete)
                    @php($isCompliant = $athlete->isCompliant())
                    <div @class([
                        'card-m3 p-4 flex items-center justify-between transition-all border-l-8',
                        'bg-white border-primary shadow-md' => $attendances[$athlete->id]['present'],
                        'bg-white border-slate-500' => !$attendances[$athlete->id]['present'],
                    ])>
                        <div class="flex items-center gap-4">
                            {{-- Foto/Avatar --}}
                            <div class="relative">
                                <div @class([
                                    'w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg',
                                    'bg-primary text-white' => $attendances[$athlete->id]['present'],
                                    'bg-slate-700 text-white' => !$attendances[$athlete->id]['present'],
                                ])>
                                    {{ substr($athlete->name, 0, 1) }}
                                </div>
                                
                                {{-- Indicador de Compliance --}}
                                @if(!$isCompliant)
                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-error rounded-full border-2 border-white flex items-center justify-center text-white" title="Pendência de Saúde ou Financeira">
                                        <span class="material-symbols-outlined text-[10px]">lock</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-bold text-secondary text-sm">{{ $athlete->name }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                    {{ $athlete->position ?? 'Posição não def.' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            @if(!$attendances[$athlete->id]['present'] && !$isCompliant)
                                <x-mary-button icon="o-chat-bubble-bottom-center-text" class="btn-ghost btn-sm text-slate-700" @click="$prompt('Justificativa para {$athlete->name}', '', (val) => $wire.set('attendances.{$athlete->id}.justification', val))" />
                            @endif

                            <button wire:click="togglePresence({{ $athlete->id }})" 
                                @class([
                                    'w-12 h-12 rounded-m3-lg flex items-center justify-center transition-all shadow-sm',
                                    'bg-primary text-white' => $attendances[$athlete->id]['present'],
                                    'bg-white text-slate-800 border-2 border-slate-700' => !$attendances[$athlete->id]['present'],
                                ])>
                                <span class="material-symbols-outlined text-2xl">
                                    {{ $attendances[$athlete->id]['present'] ? 'check_circle' : 'radio_button_unchecked' }}
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="py-20 text-center card-m3 bg-white border-2 border-dashed border-slate-100">
            <span class="material-symbols-outlined text-5xl text-slate-200 mb-4">stadium</span>
            <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Selecione uma turma para iniciar a chamada</p>
        </div>
    @endif

    {{-- Dica de Compliance --}}
    <div class="bg-secondary p-4 rounded-m3-lg text-white/60 text-[10px] font-bold uppercase tracking-widest flex items-center gap-3">
        <span class="material-symbols-outlined text-sm text-error">lock</span>
        Atletas com bloqueio possuem pendências financeiras ou atestados vencidos.
    </div>
</div>
