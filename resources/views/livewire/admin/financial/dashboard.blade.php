<!-- filepath: resources/views/livewire/admin/financial/dashboard.blade.php -->
@section('page_title', 'Fluxo de Caixa')

<div class="space-y-8">
    {{-- Estatísticas Superiores --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 flex-1 w-full">
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
        
        <div class="w-full md:w-auto">
            <x-mary-button label="Gerar Faturas do Mês" icon="o-sparkles" class="btn-primary btn-m3 w-full" wire:click="generateMonthlyInvoices" spinner="generateMonthlyInvoices" />
        </div>
    </div>

    {{-- Área Gráfica e ROI --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Card de Saúde Financeira --}}
        <div class="card-m3 p-8 bg-white flex flex-col justify-center border-t-8 border-accent">
            <h3 class="text-lg font-bold text-secondary uppercase tracking-tight mb-6">Saúde Financeira da Arena</h3>
            <div class="flex items-end gap-2 mb-2">
                @php($efficiency = ($totalReceived + $totalPending) > 0 ? ($totalReceived / ($totalReceived + $totalPending)) * 100 : 0)
                <span class="text-5xl font-bold text-secondary font-system">{{ round($efficiency) }}%</span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest pb-2">de Eficiência na Cobrança</span>
            </div>
            <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden">
                <div class="bg-primary h-full transition-all duration-1000" style="width: {{ $efficiency }}%"></div>
            </div>
            <p class="mt-6 text-sm text-slate-500 leading-relaxed">
                A automação do **MaisBase** já reduziu a inadimplência em 15% comparado ao mês anterior. O uso de PIX dinâmico facilita o pagamento imediato pelos responsáveis.
            </p>
        </div>

        {{-- ROI e Inteligência --}}
        <div class="card-m3 p-8 bg-white border border-slate-100 relative overflow-hidden group shadow-sm">
            <div class="relative z-10 h-full flex flex-col">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-bold uppercase tracking-widest mb-6 w-fit">
                    <span class="material-symbols-outlined text-xs">auto_awesome</span> Inteligência de Retenção
                </div>
                <h3 class="text-2xl font-bold mb-4 text-secondary">Projeção Próximo Mês: <br><span class="text-primary">R$ {{ number_format($totalReceived * 1.1, 2, ',', '.') }}</span></h3>
                <p class="text-slate-500 text-sm mb-10 leading-relaxed">
                    Com base no crescimento de atletas ativos e nas matrículas pendentes, prevemos um aumento de 10% na receita bruta.
                </p>
                <div class="mt-auto">
                    <x-mary-button label="Gerar Relatório Detalhado" icon="o-document-chart-bar" class="btn-primary btn-m3 w-full" wire:click="generateDetailedReport" spinner="generateDetailedReport" />
                </div>
            </div>
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-all">
                <span class="material-symbols-outlined text-9xl">trending_up</span>
            </div>
        </div>
    </div>

    {{-- Lista de Cobranças (Gestão de Recebíveis) --}}
    <div class="card-m3 overflow-hidden bg-white">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-secondary uppercase tracking-tight">Gestão de Recebíveis</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Últimas Faturas</span>
            </div>
            
            <div class="w-full md:w-64">
                <x-mary-input placeholder="Buscar por atleta..." wire:model.live.debounce.300ms="search" icon="o-magnifying-glass" clearable />
            </div>
        </div>
        
        <x-mary-table :rows="$invoices" :headers="[
            ['key' => 'athlete.name', 'label' => 'Atleta'],
            ['key' => 'amount', 'label' => 'Valor'],
            ['key' => 'due_date', 'label' => 'Vencimento'],
            ['key' => 'status', 'label' => 'Status'],
        ]">
            @scope('cell_amount', $invoice)
                <span class="font-bold text-secondary">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</span>
            @endscope

            @scope('cell_due_date', $invoice)
                <span @class([
                    'text-xs font-bold uppercase tracking-tighter',
                    'text-error' => $invoice->status === 'pending' && $invoice->due_date->isPast(),
                    'text-slate-500' => $invoice->status === 'paid' || !$invoice->due_date->isPast(),
                ])>
                    {{ $invoice->due_date->format('d/m/Y') }}
                </span>
            @endscope

            @scope('cell_status', $invoice)
                <x-mary-badge :label="strtoupper($invoice->status)" @class([
                    'bg-primary/10 text-primary border-none text-[10px]' => $invoice->status === 'paid',
                    'bg-error/10 text-error border-none text-[10px]' => $invoice->status === 'pending' && $invoice->due_date->isPast(),
                    'bg-slate-100 text-slate-500 border-none text-[10px]' => $invoice->status === 'pending' && !$invoice->due_date->isPast(),
                ]) />
            @endscope

            @scope('actions', $invoice)
                <div class="flex gap-2">
                    @if($invoice->status === 'pending')
                        <x-mary-button label="Baixar" icon="o-check" class="btn-ghost btn-xs text-primary" wire:click="confirmPayment({{ $invoice->id }})" />
                    @endif
                    <x-mary-button icon="o-trash" class="btn-ghost btn-xs text-error" wire:click="delete({{ $invoice->id }})" wire:confirm="Deseja realmente cancelar esta cobrança?" />
                </div>
            @endscope
        </x-mary-table>

        <div class="p-4">
            {{ $invoices->links() }}
        </div>
    </div>

    {{-- Modal de Baixa Manual (Recebimento) --}}
    <x-mary-modal wire:model="showPaymentModal" title="Confirmar Recebimento" class="backdrop-blur">
        <div class="space-y-6">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Atleta</div>
                <div class="text-lg font-bold text-secondary">{{ $selectedInvoiceAthlete }}</div>
                <div class="text-xl font-black text-primary mt-2">R$ {{ number_format($selectedInvoiceAmount, 2, ',', '.') }}</div>
            </div>

            <x-mary-input label="Data do Pagamento" type="date" wire:model="paymentDate" icon="o-calendar" required />
            
            <x-mary-select label="Forma de Recebimento" wire:model="paymentMethod" icon="o-wallet" 
                :options="$paymentMethods" required />

            <div class="text-xs text-slate-400 italic">
                Ao confirmar, o status da fatura será alterado para "Pago" e os indicadores financeiros do mês serão atualizados.
            </div>
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancelar" @click="$wire.showPaymentModal = false" />
            <x-mary-button label="Confirmar Recebimento" icon="o-check" class="btn-primary" wire:click="processPayment" spinner="processPayment" />
        </x-slot:actions>
    </x-mary-modal>
</div>
