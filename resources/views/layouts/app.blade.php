<!-- filepath: resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="maisbase">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Meta Tags PWA -->
    <meta name="theme-color" content="#2E7D32">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    
    <title>@yield('title', 'MaisBase - Gestão de Elite')</title>
    @yield('meta')

    <!-- Fontes Profissionais -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased min-h-screen bg-surface text-text-base">

    @auth
        {{-- Layout Principal MaryUI para Áreas Autenticadas --}}
        <x-mary-main full-width>
            
            {{-- Sidebar Lateral (Desktop) - Estilo Stadium Dark --}}
            <x-slot:sidebar drawer="main-drawer" collapsible class="bg-secondary text-white">
                
                {{-- Logo/Brand --}}
                <div class="p-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-3xl">sports_soccer</span>
                    <span class="font-bold text-xl text-primary font-system tracking-tighter">MaisBase</span>
                </div>

                {{-- Menu de Navegação --}}
                <x-mary-menu activate-by-route>
                    <x-mary-menu-item title="Atletas" icon="o-users" link="/atletas" />
                    <x-mary-menu-item title="Chamada" icon="o-clipboard-document-check" link="/campo/chamada" />
                    <x-mary-menu-item title="Saúde" icon="o-heart" link="/admin/saude" />
                    <x-mary-menu-item title="IA Nudges" icon="o-cpu-chip" link="/admin/ia-performance" />
                    <x-mary-menu-item title="Financeiro" icon="o-currency-dollar" link="/financeiro/fluxo-caixa" />
                    <x-mary-menu-item title="Planos" icon="o-credit-card" link="/financeiro/planos" />
                    <x-mary-menu-item title="Equipe" icon="o-identification" link="/equipe" />
                    
                    <x-mary-menu-separator />
                    
                    <x-mary-menu-item title="Sair" icon="o-power" link="/logout" class="text-error" />
                </x-mary-menu>
            </x-slot:sidebar>

            {{-- Conteúdo Principal --}}
            <x-slot:content class="stadium-glow bg-surface">
                {{-- Top App Bar (Mobile/Desktop Header) --}}
                <x-mary-nav sticky class="md:hidden border-b border-slate-200">
                    <x-slot:brand>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">sports_soccer</span>
                            <span class="font-bold text-secondary uppercase tracking-tight">MaisBase</span>
                        </div>
                    </x-slot:brand>
                    <x-slot:actions>
                        <label for="main-drawer" class="lg:hidden me-3">
                            <span class="material-symbols-outlined cursor-pointer">menu</span>
                        </label>
                    </x-slot:actions>
                </x-mary-nav>

                {{-- Cabeçalho da Página --}}
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-secondary font-system uppercase tracking-tight">
                        @yield('page_title', 'Dashboard')
                    </h1>
                    
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">{{ auth()->user()->role }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full border-2 border-primary p-0.5">
                            <div class="w-full h-full rounded-full bg-slate-200 flex items-center justify-center text-primary font-bold text-xs">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slot de Conteúdo Livewire/Blade --}}
                <div class="min-h-[calc(100vh-200px)]">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </x-slot:content>
        </x-mary-main>

        {{-- Bottom Navigation (Mobile) - Focado no polegar --}}
        <nav class="fixed bottom-0 w-full bg-white border-t border-slate-200 md:hidden z-50 h-16 flex justify-around items-center px-2 pb-safe">
            <a href="/dashboard" class="flex flex-col items-center gap-1 {{ request()->is('dashboard') ? 'text-primary' : 'text-slate-400' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-bold">Dashboard</span>
            </a>
            <a href="/atletas" class="flex flex-col items-center gap-1 {{ request()->is('atletas') ? 'text-primary' : 'text-slate-400' }}">
                <span class="material-symbols-outlined">group</span>
                <span class="text-[10px] font-bold">Atletas</span>
            </a>
            <a href="/chamada" class="flex flex-col items-center gap-1 {{ request()->is('chamada') ? 'text-primary' : 'text-slate-400' }}">
                <span class="material-symbols-outlined">fact_check</span>
                <span class="text-[10px] font-bold">Chamada</span>
            </a>
            <a href="/financeiro" class="flex flex-col items-center gap-1 {{ request()->is('financeiro') ? 'text-primary' : 'text-slate-400' }}">
                <span class="material-symbols-outlined">payments</span>
                <span class="text-[10px] font-bold">Financeiro</span>
            </a>
        </nav>
    @else
        {{-- Conteúdo Público (Landing / Login / Register) --}}
        <main>
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    @endauth

    @livewireScripts
    <x-mary-toast />
</body>
</html>
