<?php

// filepath: app/Services/WhatsApp/WhatsAppService.php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de integração com Gateways de WhatsApp (Evolution API / Z-API).
 */
class WhatsAppService
{
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->token = env('WHATSAPP_API_TOKEN');
        $this->baseUrl = env('WHATSAPP_API_URL', 'https://api.maisbase.com.br/wa');
    }

    /**
     * Envia uma mensagem de texto simples.
     */
    public function sendMessage($to, $message)
    {
        // Higienização do número (LGPD: Ocultar nos logs se necessário)
        $cleanNumber = preg_replace('/\D/', '', $to);
        
        Log::info("WhatsApp: Enviando mensagem para " . substr($cleanNumber, 0, 4) . "****");

        // Simulação de disparo (Mock para desenvolvimento)
        if (env('APP_ENV') === 'local') {
            return ['status' => 'sent', 'id' => uniqid()];
        }

        try {
            $response = Http::withToken($this->token)->post("{$this->baseUrl}/send-text", [
                'number' => $cleanNumber,
                'message' => $message,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Erro no WhatsApp Service: " . $e->getMessage());
            return ['status' => 'error'];
        }
    }
}
