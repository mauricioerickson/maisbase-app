<?php

namespace App\Livewire\Admin\Config;

use Livewire\Component;
use App\Services\WhatsApp\WhatsAppService;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class WhatsAppConfig extends Component
{
    public $status = 'loading';
    public $qr = null;
    public $connected = false;

    public function mount()
    {
        $this->checkStatus();
    }

    /**
     * Verifica o status da conexão na ponte Baileys.
     */
    public function checkStatus()
    {
        $service = new WhatsAppService();
        $response = $service->getStatus(Auth::user()->tenant_id);

        if (isset($response['status'])) {
            $this->status = $response['status'];
            
            if ($this->status === 'connected') {
                $this->connected = true;
                $this->qr = null;
                
                // Atualiza o tenant no banco
                Auth::user()->tenant->update(['whatsapp_connected' => true]);
            } elseif ($this->status === 'qr' && isset($response['qr'])) {
                $this->qr = $response['qr'];
                $this->connected = false;
            } else {
                $this->qr = null;
                $this->connected = false;
            }
        }
    }

    /**
     * Reseta a sessão atual para permitir novo pareamento.
     */
    public function resetSession()
    {
        $service = new WhatsAppService();
        $service->disconnect(Auth::user()->tenant_id);
        
        Auth::user()->tenant->update(['whatsapp_connected' => false]);
        
        $this->status = 'loading';
        $this->qr = null;
        $this->connected = false;
        
        $this->checkStatus();
    }

    public function render()
    {
        return view('livewire.admin.config.whatsapp-config')
            ->layout('layouts.app');
    }
}
