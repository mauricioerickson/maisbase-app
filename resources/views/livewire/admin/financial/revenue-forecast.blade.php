<!-- filepath: resources/views/livewire/admin/financial/revenue-forecast.blade.php -->
@section('page_title', 'Previsão de Faturamento')

<div class="space-y-8">
    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-mary-stat
            title="Receita Recorrente (MRR)"
            description="Previsão baseada em atletas ativos"
            value="R$ {{ number_format($totalMRR, 2, ',', '.') }}"
            icon="o-arrow-trending-up"
            class="bg-white rounded-m3-lg shadow-sm border-l-4 border-primary"
        />

        <x-mary-stat
            title="Base de Atletas"
            description="Atletas com planos ativos"
            value="{{ $totalAthletes }} Atletas"
            icon="o-users"
            class="bg-white rounded-m3-lg shadow-sm"
        />
    </div>

    {{-- Detalhamento por Plano --}}
    <div class="card-m3 bg-white overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-lg font-bold text-secondary uppercase tracking-tight">Projeção por Plano</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Detalhamento da receita mensal recorrente</p>
            </div>
        </div>

        <x-mary-table :rows="$planForecasts" :headers="[
            ['key' => 'name', 'label' => 'Nome do Plano'],
            ['key' => 'athletes_count', 'label' => 'Atletas'],
            ['key' => 'amount', 'label' => 'Valor Unit.'],
            ['key' => 'due_day', 'label' => 'Dia Vcto.'],
            ['key' => 'total', 'label' => 'Total Previsto'],
        ]">
            @scope('cell_amount', $forecast)
                <span class="text-slate-500">R$ {{ number_format($forecast['amount'], 2, ',', '.') }}</span>
            @endscope

            @scope('cell_total', $forecast)
                <span class="font-bold text-secondary">R$ {{ number_format($forecast['total'], 2, ',', '.') }}</span>
            @endscope

            @scope('cell_due_day', $forecast)
                <x-mary-badge label="Dia {{ $forecast['due_day'] }}" class="bg-slate-100 text-slate-500 border-none font-bold" />
            @endscope
        </x-mary-table>
    </div>

    {{-- Inteligência Financeira --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 card-m3 p-8 bg-secondary text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-xl font-bold mb-4 uppercase tracking-tight">Análise de Crescimento</h3>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">
                    Sua arena possui um ticket médio de **R$ {{ $totalAthletes > 0 ? number_format($totalMRR / $totalAthletes, 2, ',', '.') : '0,00' }}**. 
                    Para atingir a meta de R$ {{ number_format($totalMRR * 1.2, 2, ',', '.') }} no próximo mês, você precisa converter aproximadamente 
                    {{ ceil(($totalMRR * 0.2) / ($totalAthletes > 0 ? ($totalMRR / $totalAthletes) : 100)) }} novos atletas.
                </p>
                <div class="flex gap-4">
                    <x-mary-button label="Otimizar Planos" icon="o-adjustments-horizontal" class="btn-primary btn-sm" link="/financeiro/planos" />
                </div>
            </div>
            <span class="material-symbols-outlined absolute top-1/2 right-0 -translate-y-1/2 text-[200px] opacity-5 -mr-10">insights</span>
        </div>

        <div class="card-m3 p-8 bg-white border-t-8 border-accent">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Concentração de Receita</h3>
            <div class="space-y-6">
                @foreach($planForecasts as $forecast)
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-bold uppercase">
                            <span class="text-secondary">{{ $forecast['name'] }}</span>
                            <span class="text-slate-400">{{ $totalMRR > 0 ? round(($forecast['total'] / $totalMRR) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full" style="width: {{ $totalMRR > 0 ? ($forecast['total'] / $totalMRR) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
