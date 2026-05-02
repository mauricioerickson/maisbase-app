<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'MaisBase'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <!-- Scripts (Vite + Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary: #2E7D32;
            --secondary: #1565C0;
            --tertiary: #FBC02D;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC; /* Slate 50 */
            color: #1E293B; /* Slate 800 */
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Roboto', sans-serif;
        }
        /* Material Symbols style override to make it consistent */
        .material-symbols-outlined {
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
        }
        /* Mobile padding adjustment for bottom nav */
        .has-bottom-nav {
            padding-bottom: 80px; /* Space for the bottom navbar */
        }
    </style>
</head>
<body class="antialiased {{ Auth::check() ? 'has-bottom-nav' : '' }}">

    <!-- Page Content -->
    <main>
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Mobile Bottom Navigation (Visible only if logged in) -->
    @auth
    <nav class="fixed bottom-0 w-full bg-white border-t border-gray-200 md:hidden z-50 rounded-t-[16px] shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <div class="flex justify-around items-center h-16">
            <a href="#" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-[#2E7D32] transition-colors">
                <span class="material-symbols-outlined">home</span>
                <span class="text-xs font-medium mt-1">Início</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-[#2E7D32] transition-colors">
                <span class="material-symbols-outlined">group</span>
                <span class="text-xs font-medium mt-1">Turmas</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-[#2E7D32] transition-colors">
                <span class="material-symbols-outlined">payments</span>
                <span class="text-xs font-medium mt-1">Finanças</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-[#2E7D32] transition-colors">
                <span class="material-symbols-outlined">person</span>
                <span class="text-xs font-medium mt-1">Perfil</span>
            </a>
        </div>
    </nav>
    @endauth

    @livewireScripts
</body>
</html>
