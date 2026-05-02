@extends('layouts.app')

@section('title', 'MaisBase - Gestão para Escolas de Futebol em ' . $cidade)

@section('content')
<div class="bg-white text-slate-800">
    <!-- 1. Hero Section -->
    <section class="relative bg-gradient-to-br from-[#2E7D32] to-[#1565C0] text-white pt-24 pb-32 px-6 overflow-hidden">
        <!-- Abstract Background Shape -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="max-w-7xl mx-auto relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-white/20 backdrop-blur-md text-sm font-medium mb-6">
                A solução definitiva para {{ $cidade }}
            </span>
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                MaisBase - Gestão para Escolas de Futebol em <span class="text-[#FBC02D]">{{ $cidade }}</span>
            </h1>
            <p class="text-xl md:text-2xl text-blue-50 max-w-3xl mx-auto mb-10">
                Reduza a inadimplência a zero e pare de perder alunos (Churn) com a nossa plataforma de gestão inteligente voltada exclusivamente para o esporte.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#planos" class="bg-[#FBC02D] text-slate-900 font-semibold py-4 px-8 rounded-xl shadow-lg hover:bg-yellow-400 transition transform hover:-translate-y-1 text-lg">
                    Transformar Minha Escola
                </a>
                <a href="#nudge" class="bg-white/20 backdrop-blur-md text-white font-medium py-4 px-8 rounded-xl hover:bg-white/30 transition text-lg flex items-center gap-2">
                    <span class="material-symbols-outlined">play_circle</span>
                    Ver Demonstração
                </a>
            </div>
        </div>
    </section>

    <!-- 2. Card Visual "ROI da IA" -->
    <section class="py-20 px-6 bg-slate-50 relative -mt-16 z-20">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] p-8 md:p-12 border border-slate-100 flex flex-col md:flex-row items-center gap-12">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold mb-4 text-[#2E7D32]">Inteligência Artificial que Paga a Mensalidade</h2>
                    <p class="text-slate-600 mb-6 text-lg">
                        Nosso sistema identifica automaticamente pagamentos em atraso e alunos com risco de evasão. Através de algoritmos avançados, o MaisBase atua antes que o problema se torne irrecuperável.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#1565C0]">check_circle</span>
                            <span class="text-slate-700 font-medium">Redução de até 80% na Inadimplência</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#1565C0]">check_circle</span>
                            <span class="text-slate-700 font-medium">Reconciliação automática de PIX</span>
                        </li>
                    </ul>
                </div>
                <div class="flex-1 w-full relative">
                    <!-- ROI Card Simulation -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-xl p-6 text-white shadow-xl relative overflow-hidden transform md:rotate-2 hover:rotate-0 transition duration-500">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-[#2E7D32]/20 rounded-full blur-2xl"></div>
                        <h3 class="text-slate-400 text-sm font-medium mb-1 uppercase tracking-wider">ROI da IA (Últimos 30 dias)</h3>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-4xl font-bold text-[#FBC02D]">R$ 4.250,00</span>
                            <span class="text-green-400 text-sm font-medium flex items-center">
                                <span class="material-symbols-outlined text-sm">trending_up</span> +34%
                            </span>
                        </div>
                        <div class="space-y-3 relative z-10">
                            <div class="bg-white/10 rounded-lg p-3 flex justify-between items-center backdrop-blur-sm border border-white/5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-green-400 text-sm">attach_money</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-300">Mensalidades Recuperadas</p>
                                        <p class="text-sm font-semibold">18 cobranças automáticas</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-medium">+ R$ 2.700,00</span>
                            </div>
                            <div class="bg-white/10 rounded-lg p-3 flex justify-between items-center backdrop-blur-sm border border-white/5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-blue-400 text-sm">group_add</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-300">Evasão Evitada (Churn)</p>
                                        <p class="text-sm font-semibold">5 alunos engajados</p>
                                    </div>
                                </div>
                                <span class="text-blue-400 font-medium">+ R$ 1.550,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Section Nudge Financeiro via WhatsApp -->
    <section id="nudge" class="py-24 px-6 bg-white">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-16">
            <div class="flex-1 w-full max-w-sm mx-auto">
                <!-- Phone Mockup -->
                <div class="bg-slate-900 rounded-[2.5rem] border-[8px] border-slate-800 shadow-2xl relative overflow-hidden h-[550px] w-full flex flex-col">
                    <div class="bg-emerald-600 px-4 py-3 text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                        <div class="flex-1 flex items-center gap-2">
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-emerald-600 font-bold">M</div>
                            <div>
                                <p class="font-medium text-sm leading-tight">MaisBase Escolinha</p>
                                <p class="text-[10px] text-emerald-100">Conta Comercial</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 bg-[#E5DDD5] p-4 flex flex-col gap-4 overflow-y-auto" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');">
                        <!-- Message 1 -->
                        <div class="bg-white rounded-lg rounded-tl-none p-3 max-w-[85%] shadow-sm self-start relative">
                            <p class="text-sm text-slate-800">Olá Carlos! Tudo bem? Passando para lembrar que a mensalidade do João vence amanhã (10/05). ⚽</p>
                            <span class="text-[10px] text-slate-400 absolute bottom-1 right-2">09:14</span>
                        </div>
                        <!-- Message 2 (Pix) -->
                        <div class="bg-white rounded-lg p-3 max-w-[85%] shadow-sm self-start relative">
                            <p class="text-sm text-slate-800 mb-2">Aqui está o código PIX Copia e Cola para facilitar o pagamento:</p>
                            <div class="bg-slate-100 p-2 rounded text-xs text-slate-500 font-mono break-all mb-2">
                                00020126580014br.gov.bcb.pix0136...
                            </div>
                            <button class="w-full bg-[#1565C0] text-white text-xs py-2 rounded flex justify-center items-center gap-1 font-medium">
                                <span class="material-symbols-outlined text-[14px]">content_copy</span> Copiar Código
                            </button>
                            <span class="text-[10px] text-slate-400 absolute bottom-1 right-2">09:14</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-[#2E7D32] text-sm font-semibold mb-4">
                    <span class="material-symbols-outlined text-sm">forum</span> Comunicação Ativa
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Nudge Financeiro via WhatsApp</h2>
                <p class="text-slate-600 text-lg mb-8 leading-relaxed">
                    Esqueça o constrangimento de cobrar os pais. O MaisBase utiliza a técnica de "Nudge" (Empurrãozinho) para enviar lembretes amigáveis e humanizados de forma 100% automatizada.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <span class="material-symbols-outlined text-[#FBC02D] text-3xl mb-3">auto_awesome</span>
                        <h4 class="font-bold text-slate-800 mb-2">Tom Personalizado</h4>
                        <p class="text-sm text-slate-600">Ajuste o tom das mensagens (Amigável, Formal ou Estrito) de acordo com o perfil da sua escola.</p>
                    </div>
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <span class="material-symbols-outlined text-[#2E7D32] text-3xl mb-3">bolt</span>
                        <h4 class="font-bold text-slate-800 mb-2">Zero Atrito</h4>
                        <p class="text-sm text-slate-600">O código PIX já vai na mensagem. Pagou, baixou automaticamente no sistema em segundos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Pricing Table -->
    <section id="planos" class="py-24 px-6 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Planos que Cabem no Orçamento da Sua Escola</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                Investimento que se paga sozinho já nas primeiras mensalidades recuperadas.
            </p>
        </div>

        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <!-- Plano Formação -->
            <div class="bg-white rounded-xl shadow-md border border-slate-200 p-8 hover:shadow-lg transition">
                <div class="mb-6">
                    <span class="text-[#1565C0] font-semibold tracking-wider uppercase text-sm">Plano Formação</span>
                    <div class="mt-4 flex items-baseline gap-1 text-slate-900">
                        <span class="text-4xl font-bold">R$ 149</span>
                        <span class="text-slate-500">/mês</span>
                    </div>
                    <p class="mt-2 text-slate-500 text-sm">Ideal para escolinhas em crescimento.</p>
                </div>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#2E7D32] shrink-0">check</span>
                        <span class="text-slate-700">Até 100 atletas cadastrados</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#2E7D32] shrink-0">check</span>
                        <span class="text-slate-700">Lembretes de cobrança via E-mail</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#2E7D32] shrink-0">check</span>
                        <span class="text-slate-700">Gestão financeira básica (Fluxo de Caixa)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#2E7D32] shrink-0">check</span>
                        <span class="text-slate-700">Controle de frequência e turmas</span>
                    </li>
                </ul>
                <button class="w-full py-3 px-4 rounded-xl border-2 border-[#1565C0] text-[#1565C0] font-semibold hover:bg-blue-50 transition">
                    Começar Gratuitamente
                </button>
            </div>

            <!-- Plano Elite -->
            <div class="bg-[#2E7D32] rounded-xl shadow-2xl p-8 relative transform md:-translate-y-4 border-2 border-green-500">
                <div class="absolute top-0 right-0 transform translate-x-2 -translate-y-3">
                    <span class="bg-[#FBC02D] text-slate-900 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                        MAIS POPULAR
                    </span>
                </div>
                <div class="mb-6">
                    <span class="text-green-100 font-semibold tracking-wider uppercase text-sm">Plano Elite</span>
                    <div class="mt-4 flex items-baseline gap-1 text-white">
                        <span class="text-4xl font-bold">R$ 249</span>
                        <span class="text-green-200">/mês</span>
                    </div>
                    <p class="mt-2 text-green-100 text-sm">A experiência completa para redução total da inadimplência.</p>
                </div>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#FBC02D] shrink-0">check</span>
                        <span class="text-white">Atletas <strong class="text-[#FBC02D]">Ilimitados</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#FBC02D] shrink-0">check</span>
                        <span class="text-white">Nudge Automático via <strong class="text-[#FBC02D]">WhatsApp</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#FBC02D] shrink-0">check</span>
                        <span class="text-white">Análise Preditiva de Evasão (IA)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#FBC02D] shrink-0">check</span>
                        <span class="text-white">Reconciliação automática de PIX via API</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[#FBC02D] shrink-0">check</span>
                        <span class="text-white">Suporte Prioritário</span>
                    </li>
                </ul>
                <button class="w-full py-3 px-4 rounded-xl bg-[#FBC02D] text-slate-900 font-bold hover:bg-yellow-400 transition shadow-lg">
                    Assinar Plano Elite
                </button>
            </div>
        </div>
    </section>

    <!-- Footer Simple -->
    <footer class="bg-slate-900 text-slate-400 py-8 text-center text-sm">
        <p>&copy; {{ date('Y') }} MaisBase. Todos os direitos reservados.</p>
    </footer>
</div>
@endsection
