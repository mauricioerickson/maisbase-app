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
        // A ponte roda localmente na porta 3000 por padrão
        $this->baseUrl = env('WHATSAPP_BRIDGE_URL', 'http://localhost:3000');
    }

    /**
     * Envia uma mensagem de texto usando a sessão do Tenant atual.
     */
    public function sendMessage($to, $message, $tenantId = null)
    {
        $tenantId = $tenantId ?: session('tenant_id');

        if (!$tenantId) {
            Log::warning("WhatsApp: Tentativa de envio sem tenant_id definido.");
            return ['status' => 'error', 'message' => 'No tenant ID'];
        }

        $cleanNumber = preg_replace('/\D/', '', $to);
        
        Log::info("WhatsApp: Enviando mensagem via Baileys para " . substr($cleanNumber, 0, 4) . "**** (Tenant: {$tenantId})");

        try {
            $response = Http::post("{$this->baseUrl}/send", [
                'tenantId' => (string) $tenantId,
                'to' => $cleanNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return ['status' => 'sent'];
            }

            Log::error("Erro no WhatsApp Bridge: " . $response->body());
            return ['status' => 'error', 'body' => $response->json()];
        } catch (\Exception $e) {
            Log::error("Erro de conexão com WhatsApp Bridge: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Connection failed'];
        }
    }

    /**
     * Obtém o QR Code ou Status da sessão do Tenant.
     */
    public function getStatus($tenantId)
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/qr/{$tenantId}");
            return $response->json();
        } catch (\Exception $e) {
            return ['status' => 'offline'];
        }
    }
    /**
     * Reseta a sessão do WhatsApp (Logout + Limpeza de arquivos).
     */
    public function disconnect($tenantId)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/session/{$tenantId}");
            return $response->json();
        } catch (\Exception $e) {
            return ['status' => 'error'];
        }
    }
}
