<!-- filepath: resources/views/landing.blade.php -->
@extends('layouts.app')

@section('title', $ogTitle ?? 'MaisBase - Gestão para Escolas de Futebol')

@section('meta')
    <meta name="description" content="{{ $metaDescription ?? 'MaisBase e o sistema de gestao para escolas de futebol com financeiro, chamada e IA.' }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle ?? 'MaisBase - Gestao de Elite' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'MaisBase e o sistema de gestao para escolas de futebol com financeiro, chamada e IA.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
@endsection

@section('content')
<div class="bg-secondary min-h-screen text-white selection:bg-primary selection:text-white">
    <!-- Navbar Dinâmica -->
    <nav class="fixed top-0 w-full z-50 bg-secondary/80 backdrop-blur-md border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Logotipo -->
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-4xl">sports_soccer</span>
                <span class="font-bold text-2xl text-white font-system tracking-tighter uppercase">Mais<span class="text-primary">Base</span></span>
            </div>

            <!-- Links Centrais -->
            <div class="hidden md:flex items-center gap-8 text-sm font-bold uppercase tracking-widest text-white/50">
                <a href="#roi" class="hover:text-white transition">IA & Retorno</a>
                <a href="#planos" class="hover:text-white transition">Planos</a>
            </div>

            <!-- Botões de Acesso -->
            <div class="flex items-center gap-6">
                {{-- Entrar (Estilo Ghost) --}}
                <a href="/login" class="btn btn-ghost text-sm font-bold text-white/70 hover:text-white transition uppercase tracking-widest border-none">Entrar</a>
                
                {{-- Registrar Arena (Estilo Contained Pitch Green) --}}
                <a href="/register" class="btn btn-primary btn-m3 text-sm font-bold shadow-xl uppercase tracking-wider">
                    Registrar Arena
                </a>
            </div>
        </div>
    </nav>

    <!-- 1. Hero Dynamic -->
    <section class="relative pt-40 pb-32 px-6 overflow-hidden stadium-glow">
        <div class="max-w-7xl mx-auto text-center relative z-10">
            <span class="inline-block py-2 px-4 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-primary text-xs font-bold uppercase tracking-[0.2em] mb-8">
                Performance Profissional em {{ $cidade ?? 'Sua Região' }}
            </span>
            <h1 class="text-5xl md:text-8xl font-bold mb-8 leading-[0.9] tracking-tighter">
                Gestão Inteligente <br> 
                em <span class="text-accent">{{ $cidade ?? 'Sua Região' }}</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-3xl mx-auto mb-12 font-medium leading-relaxed">
                A solução definitiva para reduzir a inadimplência e o churn de atletas. Tecnologia de elite para a sua arena.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="/register" class="btn btn-primary btn-m3 h-auto py-5 px-10 shadow-[0_20px_50px_rgba(46,125,50,0.3)] hover:scale-105 transition-all text-lg uppercase tracking-widest border-none">
                    Começar Onboarding
                </a>
                <a href="#roi" class="btn btn-outline btn-m3 h-auto py-5 px-10 border-white/10 text-white hover:bg-white/10 transition-all text-lg flex items-center gap-3 uppercase tracking-widest">
                    <span class="material-symbols-outlined">play_circle</span>
                    Ver Demonstração
                </a>
            </div>
        </div>
    </section>

    <!-- 2. Card ROI - Receita Recuperada pela IA -->
    <section id="roi" class="py-32 px-6 bg-[#121411]">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest mb-6">
                    <span class="material-symbols-outlined text-sm">auto_graph</span> Retorno Financeiro
                </div>
                <h2 class="text-4xl md:text-5xl font-bold mb-8 leading-tight">Sua Escola se paga <br> com nossa Inteligência</h2>
                <p class="text-slate-400 text-lg mb-10 leading-relaxed">
                    Nossa IA recupera mensalidades em atraso e evita a saída de alunos através de análises preditivas de engajamento.
                </p>
            </div>

            {{-- Card ROI Visual --}}
            <div class="relative">
                <div class="bg-white rounded-m3-lg p-10 shadow-2xl border-l-8 border-primary">
                    <div class="flex justify-between items-start mb-8">
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Receita Recuperada (Mês Atual)</span>
                        <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-[10px] font-bold">+28.4% vs mês ant.</span>
                    </div>
                    
                    <div class="text-5xl font-bold text-secondary mb-4 font-system tracking-tight">R$ 5.420,00</div>
                    
                    <div class="flex items-center gap-3 mb-8">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-primary/20 border-2 border-white flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-xs">payments</span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-accent/20 border-2 border-white flex items-center justify-center">
                                <span class="material-symbols-outlined text-accent text-xs">workspace_premium</span>
                            </div>
                        </div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-tighter">Impacto direto no seu EBITDA</p>
                    </div>

                    {{-- Progressão Visual --}}
                    <div class="space-y-4">
                        <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-primary to-accent w-[82%]"></div>
                        </div>
                        <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>Inadimplência Reduzida</span>
                            <span>82% de Sucesso</span>
                        </div>
                    </div>
                </div>
                
                {{-- Badge de Performance --}}
                <div class="absolute -bottom-6 -right-6 bg-accent text-secondary p-4 rounded-m3-lg shadow-xl hidden sm:flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl">emoji_events</span>
                    <div class="leading-none">
                        <p class="text-[10px] font-bold uppercase opacity-60">Status</p>
                        <p class="text-sm font-bold uppercase tracking-tighter">Arena de Elite</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- 3. Seção de Planos -->
    <section id="planos" class="py-32 px-6 bg-secondary relative overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-6xl font-bold mb-6 tracking-tighter uppercase">Escolha sua <span class="text-primary">Categoria</span></h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Planos flexíveis para arenas de todos os tamanhos. Comece sua transformação digital hoje.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Plano Base -->
                <div class="bg-white/5 border border-white/10 p-8 rounded-m3-lg flex flex-col hover:bg-white/10 transition-all group">
                    <h3 class="text-xl font-bold mb-2 uppercase tracking-widest text-slate-300">Plano Base</h3>
                    <div class="text-4xl font-bold mb-6 font-system">Gratuito</div>
                    <p class="text-slate-400 text-sm mb-8 flex-1">Ideal para pequenas escolas iniciando a organização digital.</p>
                    <ul class="space-y-4 mb-10 text-sm font-medium">
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Até 20 Atletas</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Gestão de Frequência</li>
                        <li class="flex items-center gap-3 opacity-30"><span class="material-symbols-outlined text-sm">cancel</span> Recuperação por IA</li>
                    </ul>
                    <a href="/register" class="btn btn-outline border-white/20 text-white w-full btn-m3 uppercase text-xs tracking-widest group-hover:bg-white group-hover:text-secondary">Começar Grátis</a>
                </div>

                <!-- Plano Arena (Popular) -->
                <div class="bg-white p-8 rounded-m3-lg flex flex-col shadow-2xl relative transform scale-105 z-20 border-t-8 border-primary">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-white px-4 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Mais Popular</div>
                    <h3 class="text-xl font-bold mb-2 uppercase tracking-widest text-secondary">Plano Arena</h3>
                    <div class="text-4xl font-bold mb-2 font-system text-secondary">R$ 147<span class="text-lg opacity-50">/mês</span></div>
                    <p class="text-slate-500 text-sm mb-8 flex-1">Gestão completa para arenas em crescimento com foco em redução de churn.</p>
                    <ul class="space-y-4 mb-10 text-sm font-bold text-secondary">
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Atletas Ilimitados</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Gestão Financeira Completa</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Relatórios de Performance</li>
                    </ul>
                    <a href="/register" class="btn btn-primary w-full btn-m3 uppercase text-xs tracking-widest shadow-lg">Contratar Arena</a>
                </div>

                <!-- Plano Elite -->
                <div class="bg-white/5 border border-accent/30 p-8 rounded-m3-lg flex flex-col hover:bg-accent/5 transition-all group">
                    <h3 class="text-xl font-bold mb-2 uppercase tracking-widest text-accent">Plano Elite</h3>
                    <div class="text-4xl font-bold mb-2 font-system text-white">R$ 297<span class="text-lg opacity-50">/mês</span></div>
                    <p class="text-slate-400 text-sm mb-8 flex-1">A potência total do MaisBase com IA avançada para recuperação de receita.</p>
                    <ul class="space-y-4 mb-10 text-sm font-medium">
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-accent text-sm">workspace_premium</span> Recuperação de IA Ativa</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-accent text-sm">check_circle</span> Consultoria de Retenção</li>
                        <li class="flex items-center gap-3"><span class="material-symbols-outlined text-accent text-sm">check_circle</span> App Customizado White-label</li>
                    </ul>
                    <a href="/register" class="btn btn-outline border-accent/50 text-accent w-full btn-m3 uppercase text-xs tracking-widest hover:bg-accent hover:text-secondary">Assinar Elite</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Simples -->
    <footer class="py-12 border-t border-white/5 bg-[#0a0b09] text-center">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">sports_soccer</span>
                <span class="font-bold text-lg text-white font-system tracking-tighter uppercase">MaisBase</span>
            </div>
            <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} MaisBase.com.br - Gestão Profissional.</p>
        </div>
    </footer>
</div>
@endsection
