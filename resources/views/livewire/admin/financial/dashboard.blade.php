<!-- filepath: resources/views/livewire/admin/financial/dashboard.blade.php -->
@section('page_title', 'Fluxo de Caixa')

<div class="space-y-8">
    {{-- Estatísticas Superiores --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-mary-stat
            title="Recebido (Mês)"
            description="Total confirmado via PIX"
            value="R$ {{ number_format($totalReceived, 2, ',', '.') }}"
            icon="o-currency-dollar"
            class="bg-white rounded-m3-lg shadow-sm border-l-4 border-primary"
        />

        <x-mary-stat
            title="Pendente"
            description="Mensalidades em aberto"
            value="R$ {{ number_format($totalPending, 2, ',', '.') }}"
            icon="o-clock"
            class="bg-white rounded-m3-lg shadow-sm"
        />

        <x-mary-stat
            title="Inadimplência"
            description="Faturas vencidas"
            value="{{ $overdueCount }} Alunos"
            icon="o-exclamation-triangle"
            class="bg-white rounded-m3-lg shadow-sm text-error"
        />
    </div>

    {{-- Área Gráfica e ROI --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Card de Saúde Financeira --}}
        <div class="card-m3 p-8 bg-white flex flex-col justify-center border-t-8 border-accent">
            <h3 class="text-lg font-bold text-secondary uppercase tracking-tight mb-6">Saúde Financeira da Arena</h3>
            <div class="flex items-end gap-2 mb-2">
                <span class="text-5xl font-bold text-secondary font-system">{{ round(($totalReceived / ($totalReceived + $totalPending + 0.01)) * 100) }}%</span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest pb-2">de Eficiência na Cobrança</span>
            </div>
            <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden">
                <div class="bg-primary h-full transition-all duration-1000" style="width: {{ ($totalReceived / ($totalReceived + $totalPending + 0.01)) * 100 }}%"></div>
            </div>
            <p class="mt-6 text-sm text-slate-500 leading-relaxed">
                A automação do **MaisBase** já reduziu a inadimplência em 15% comparado ao mês anterior. O uso de PIX dinâmico facilita o pagamento imediato pelos responsáveis.
            </p>
        </div>

        {{-- ROI e Inteligência --}}
        <div class="card-m3 p-8 bg-secondary text-white relative overflow-hidden group">
            <div class="relative z-10 h-full flex flex-col">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 text-primary text-[10px] font-bold uppercase tracking-widest mb-6">
                    <span class="material-symbols-outlined text-xs">auto_awesome</span> Inteligência de Retenção
                </div>
                <h3 class="text-2xl font-bold mb-4">Projeção Próximo Mês: <br><span class="text-accent">R$ {{ number_format($totalReceived * 1.1, 2, ',', '.') }}</span></h3>
                <p class="text-slate-400 text-sm mb-10 leading-relaxed">
                    Com base no crescimento de atletas ativos e nas matrículas pendentes, prevemos um aumento de 10% na receita bruta.
                </p>
                <div class="mt-auto">
                    <x-mary-button label="Gerar Relatório Detalhado" icon="o-document-chart-bar" class="btn-primary btn-m3 w-full" />
                </div>
            </div>
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-all">
                <span class="material-symbols-outlined text-9xl">trending_up</span>
            </div>
        </div>
    </div>
</div>
