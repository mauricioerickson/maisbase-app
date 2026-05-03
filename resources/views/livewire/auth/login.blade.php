<div class="min-h-screen flex items-center justify-center bg-surface py-12 px-4 sm:px-6 lg:px-8 stadium-glow">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-m3-lg shadow-2xl border border-slate-100">
        <div>
            <div class="flex justify-center text-primary text-6xl">
                <span class="material-symbols-outlined">sports_soccer</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold text-secondary font-system uppercase tracking-tight">
                Acesse sua Escola
            </h2>
            <p class="mt-2 text-center text-sm text-slate-400 font-medium">
                Gestão Profissional MaisBase
            </p>
        </div>
        
        <form class="mt-8 space-y-6" wire:submit.prevent="login">
            <div class="space-y-4">
                <x-mary-input label="E-mail" wire:model="email" icon="o-envelope" inline required />
                <x-mary-input label="Senha" type="password" wire:model="password" icon="o-key" inline required />
            </div>
            
            @if($errors->has('email'))
                <div class="bg-error/10 text-error p-3 rounded-lg text-xs font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">error</span>
                    {{ $errors->first('email') }}
                </div>
            @endif

            <div class="pt-2">
                <x-mary-button type="submit" class="btn-primary w-full btn-m3 uppercase tracking-widest text-sm" spinner>
                    Entrar em Campo
                </x-mary-button>
            </div>
            
            <div class="text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                Novo por aqui? 
                <a href="/register" class="text-primary hover:underline">Registrar Minha Arena</a>
            </div>
        </form>
    </div>
</div>
