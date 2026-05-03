<!-- filepath: resources/views/livewire/admin/reports/ai-performance.blade.php -->
@section('page_title', 'Performance de IA')

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-secondary font-system uppercase tracking-tight">Motor de Nudges</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Inteligência Artificial aplicada à retenção e receita</p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-full text-primary text-xs font-bold uppercase tracking-widest">
            <span class="material-symbols-outlined text-sm animate-pulse">auto_awesome</span> Gemini 1.5 Ativo
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-mary-stat title="Mensagens Enviadas" value="{{ $totalSent }}" icon="o-paper-airplane" class="bg-white rounded-m3-lg shadow-sm" />
        <x-mary-stat title="Régua de Cobrança" value="{{ $billingNudges }}" icon="o-currency-dollar" class="bg-white rounded-m3-lg shadow-sm" />
        <x-mary-stat title="Reengajamento" value="{{ $retentionNudges }}" icon="o-heart" class="bg-white rounded-m3-lg shadow-sm" />
        <x-mary-stat title="Recuperado (ROI)" value="R$ {{ number_format($recoveredRevenue, 2, ',', '.') }}" icon="o-banknotes" class="bg-white rounded-m3-lg shadow-sm border-l-4 border-accent" />
    </div>

    {{-- Detalhamento IA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 card-m3 p-8 bg-white">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Últimas Ações da IA</h3>
            <div class="space-y-4">
                @foreach(\App\Models\AiNudgeLog::with('athlete')->latest()->take(5)->get() as $log)
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 flex gap-4">
                        <div @class([
                            'w-10 h-10 rounded-full flex items-center justify-center text-white shrink-0',
                            'bg-primary' => $log->type === 'billing_reminder' || $log->type === 'billing_overdue',
                            'bg-accent' => $log->type === 'retention',
                        ])>
                            <span class="material-symbols-outlined text-sm">
                                {{ $log->type === 'retention' ? 'favorite' : 'payments' }}
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-secondary">{{ $log->athlete->name }}</span>
                                <span class="text-[10px] text-slate-400">• {{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 italic leading-relaxed">"{{ $log->message }}"</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-m3 p-8 bg-secondary text-white relative overflow-hidden flex flex-col justify-center">
            <div class="relative z-10 text-center space-y-6">
                <span class="material-symbols-outlined text-6xl text-accent">workspace_premium</span>
                <h3 class="text-2xl font-bold tracking-tight">Eficiência de 98%</h3>
                <p class="text-slate-400 text-xs leading-relaxed">
                    Nossas variações de texto geradas pelo Gemini reduziram o bloqueio de números em quase 100%, mantendo a Arena sempre em contato com os pais.
                </p>
                <div class="pt-6">
                    <x-mary-button label="Otimizar Prompts" class="btn-primary btn-m3 w-full" />
                </div>
            </div>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-accent/5 rounded-full blur-3xl"></div>
        </div>
    </div>
</div>
