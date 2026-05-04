<!-- filepath: resources/views/livewire/admin/config/whatsapp-config.blade.php -->
@section('page_title', 'Configuração WhatsApp')

<div class="max-w-4xl mx-auto space-y-8" wire:poll.5s="checkStatus">
    <div class="card-m3 p-8 bg-white border-t-4 border-primary shadow-xl">
        <div class="flex flex-col md:flex-row gap-8 items-center">
            
            {{-- Lado Esquerdo: Instruções --}}
            <div class="flex-1 space-y-6">
                <div>
                    <h2 class="text-2xl font-bold text-secondary mb-2">Conectar WhatsApp da Arena</h2>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Conecte o WhatsApp do gestor para automatizar cobranças, lembretes de aula e avisos de inadimplência.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">1</div>
                        <p class="text-sm text-secondary font-medium">Abra o WhatsApp no seu celular.</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">2</div>
                        <p class="text-sm text-secondary font-medium">Toque em <b>Aparelhos Conectados</b> e <b>Conectar um Aparelho</b>.</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">3</div>
                        <p class="text-sm text-secondary font-medium">Aponte a câmera para o QR Code ao lado.</p>
                    </div>
                </div>

                @if($connected)
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-center gap-3 text-green-700">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="text-sm font-bold uppercase tracking-tight">WhatsApp Conectado com Sucesso!</span>
                    </div>
                @endif
            </div>

            {{-- Lado Direito: QR Code --}}
            <div class="w-full md:w-80 flex flex-col items-center justify-center bg-slate-50 rounded-2xl p-8 border-2 border-dashed border-slate-200 relative min-h-[300px]">
                
                @if($status === 'loading')
                    <x-mary-loading class="text-primary loading-lg" />
                    <p class="mt-4 text-xs font-bold text-slate-400 uppercase">Iniciando ponte...</p>
                @elseif($connected)
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto text-primary">
                            <span class="material-symbols-outlined text-5xl">phonelink_ring</span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Sessão Ativa</p>
                        <x-mary-button label="Desconectar" icon="o-power" class="btn-error btn-sm" wire:click="resetSession" spinner="resetSession" />
                    </div>
                @elseif($qr)
                    <div class="bg-white p-4 rounded-xl shadow-inner mb-4">
                        {!! QrCode::size(200)->generate($qr) !!}
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase animate-pulse">Aguardando leitura do QR Code...</p>
                @else
                    <div class="text-center">
                        <span class="material-symbols-outlined text-5xl text-slate-300 mb-4">qr_code_2</span>
                        <p class="text-xs font-bold text-slate-400 uppercase">Gerando novo código...</p>
                    </div>
                @endif

                {{-- Overlay de Refresh --}}
                <div class="absolute bottom-4 right-4">
                    <button wire:click="resetSession" class="w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center text-slate-400 hover:text-primary transition-colors" title="Gerar novo QR Code">
                        <span class="material-symbols-outlined text-sm">refresh</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Dicas de Segurança --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-m3 p-6 bg-secondary text-white">
            <h4 class="text-sm font-bold mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">security</span> Segurança dos Dados
            </h4>
            <p class="text-xs text-slate-400 leading-relaxed">
                O MaisBase não armazena suas mensagens. A conexão é direta entre o servidor e o seu WhatsApp via API Baileys.
            </p>
        </div>
        <div class="card-m3 p-6 bg-white border border-slate-200">
            <h4 class="text-sm font-bold text-secondary mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">auto_mode</span> Automação Ativa
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed">
                Assim que conectado, o sistema passará a enviar lembretes automáticos baseados nas suas configurações de IA.
            </p>
        </div>
    </div>
</div>
