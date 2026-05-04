<!-- filepath: resources/views/livewire/admin/dashboard.blade.php -->
@section('page_title', 'Visão Geral Arena')

<div class="space-y-8">
    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-m3 p-6 flex items-center gap-6">
            <div class="w-14 h-14 bg-primary/10 rounded-m3-lg flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">group</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Atletas</p>
                <h3 class="text-2xl font-bold text-secondary">{{ $totalAthletes }}</h3>
            </div>
        </div>

        <div class="card-m3 p-6 flex items-center gap-6 border-l-4 border-primary">
            <div class="w-14 h-14 bg-primary/10 rounded-m3-lg flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">payments</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Receita Recebida</p>
                <h3 class="text-2xl font-bold text-secondary">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</h3>
            </div>
        </div>

        <div class="card-m3 p-6 flex items-center gap-6">
            <div class="w-14 h-14 bg-error/10 rounded-m3-lg flex items-center justify-center text-error">
                <span class="material-symbols-outlined text-3xl">priority_high</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pendências</p>
                <h3 class="text-2xl font-bold text-secondary">{{ $pendingInvoices }} Faturas</h3>
            </div>
        </div>
    </div>

    {{-- ROI Card e Retenção --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Card de IA --}}
        <div class="card-m3 p-8 bg-secondary text-white relative overflow-hidden">
            <div class="relative z-10 flex flex-col h-full">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 text-primary text-[10px] font-bold uppercase tracking-widest mb-4 w-fit">
                    <span class="material-symbols-outlined text-xs">auto_awesome</span> Inteligência Artificial Ativa
                </div>
                <h2 class="text-3xl font-bold mb-4 tracking-tight">Evasão Sob Controle</h2>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">
                    Nossa IA detectou atletas com alto risco de evasão baseado nas faltas recentes.
                </p>
                <div class="mt-auto">
                    <x-mary-button label="Ver Relatório Completo" icon="o-chart-bar" class="btn-primary btn-m3 w-full" wire:click="viewFullReport" spinner="viewFullReport" />
                </div>
            </div>
        </div>

        {{-- Lista de Atletas em Risco --}}
        <div class="card-m3 p-6 bg-white border-l-8 border-error">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Top Alertas de Evasão (Churn)</h3>
            <div class="space-y-4">
                @forelse($atRiskAthletes as $athlete)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-error animate-pulse"></div>
                            <div>
                                <p class="text-sm font-bold text-secondary">{{ $athlete->name }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Score: {{ $athlete->risk_score }} / 100</p>
                            </div>
                        </div>
                        <x-mary-button label="Reengajar" icon="o-paper-airplane" class="btn-ghost btn-xs text-primary font-bold" />
                    </div>
                @empty
                    <div class="text-center py-10">
                        <span class="material-symbols-outlined text-3xl text-primary/20 mb-2">check_circle</span>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Nenhum risco detectado</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
