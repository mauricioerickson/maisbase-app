<!-- filepath: resources/views/livewire/auth/register-arena.blade.php -->
<div class="min-h-screen flex items-center justify-center bg-surface py-12 px-4 sm:px-6 lg:px-8 stadium-glow">
    <div class="max-w-xl w-full space-y-8 bg-white p-10 rounded-m3-lg shadow-2xl border border-slate-100">
        <div class="text-center">
            <div class="flex justify-center text-primary text-6xl mb-4">
                <span class="material-symbols-outlined">stadium</span>
            </div>
            <h2 class="text-3xl font-bold text-secondary font-system uppercase tracking-tight">
                Cadastrar Sua Arena
            </h2>
            <p class="mt-2 text-sm text-slate-400 font-medium">
                Junte-se à elite da gestão de bases de futebol
            </p>
        </div>
        
        <form class="mt-8 space-y-6" wire:submit.prevent="register">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Dados da Arena --}}
                <div class="space-y-4 md:col-span-2">
                    <div class="px-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Informações da Escola</div>
                    <x-mary-input label="Nome da Arena" wire:model.live="arena_name" icon="o-building-office-2" inline required />
                    <x-mary-input label="URL da Escola (Slug)" wire:model="slug" icon="o-link" prefix="maisbase.com.br/arena/" inline required />
                    <x-mary-input label="WhatsApp (Com DDD)" wire:model="whatsapp" icon="o-phone" inline required />
                </div>

                {{-- Dados do Gestor --}}
                <div class="space-y-4 md:col-span-2 pt-4">
                    <div class="px-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Acesso do Gestor (Admin)</div>
                    <x-mary-input label="Nome do Gestor" wire:model="manager_name" icon="o-user" inline required />
                    <x-mary-input label="E-mail Profissional" type="email" wire:model="email" icon="o-envelope" inline required />
                </div>

                <div class="space-y-4">
                    <x-mary-input label="Senha" type="password" wire:model="password" icon="o-key" inline required />
                </div>
                <div class="space-y-4">
                    <x-mary-input label="Confirmar Senha" type="password" wire:model="password_confirmation" icon="o-key" inline required />
                </div>
            </div>

            <div class="pt-6">
                <x-mary-button type="submit" class="btn-primary w-full btn-m3 uppercase tracking-widest text-sm shadow-lg h-14" spinner="register">
                    Finalizar Cadastro de Elite
                </x-mary-button>
            </div>
            
            <div class="text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                Já faz parte da rede? 
                <a href="/login" class="text-primary hover:underline">Fazer Login Administrativo</a>
            </div>
        </form>
    </div>
</div>
