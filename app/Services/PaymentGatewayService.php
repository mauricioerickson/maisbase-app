<?php

// filepath: app/Services/PaymentGatewayService.php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Str;

/**
 * Serviço genérico para integração com Gateways de Pagamento (PIX).
 * Simula a integração para fins de desenvolvimento.
 */
class PaymentGatewayService
{
    /**
     * Gera uma cobrança PIX para uma Invoice.
     */
    public function generatePixCharge(Invoice $invoice)
    {
        // Aqui haveria uma chamada real para Asaas, Mercado Pago, etc.
        // Simulando resposta do Gateway:
        $externalId = 'ext_' . Str::random(10);
        $pixCode = '00020101021226850014br.gov.bcb.pix2563qrcode.example.com/qr/v2/9234567890abcdef' . Str::random(20);

        $invoice->update([
            'external_id' => $externalId,
            'pix_copy_paste' => $pixCode,
        ]);

        return [
            'external_id' => $externalId,
            'pix_code' => $pixCode,
        ];
    }
}
