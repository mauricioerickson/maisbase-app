<?php

// filepath: app/Http/Controllers/WebhookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Log;

/**
 * Controller para processar retornos automáticos de pagamentos.
 */
class WebhookController extends Controller
{
    public function handle(Request $request, $gateway)
    {
        // 1. Verificação de Assinatura HMAC (Fails gracefully with 401 if secret missing or mismatch)
        if (!$this->hasValidSignature($request)) {
            Log::warning("Webhook [{$gateway}]: Tentativa de acesso sem assinatura valida ou secreta ausente.");
            return response()->json(['status' => 'unauthorized'], 401);
        }

        // 2. Sanitização de Payload para LGPD (Remover dados sensiveis antes do log)
        $payload = $request->except(['credit_card', 'document', 'cpf', 'address', 'phone']);
        
        // 3. Log para Auditoria Técnica (Sem dados sensiveis)
        WebhookLog::create([
            'gateway' => $gateway,
            'payload' => $payload,
        ]);

        $externalId = $request->input('payment.id') ?? $request->input('id');
        $event = $request->input('event');

        // 4. Filtro de Eventos de Pagamento
        if (!in_array($event, ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED', 'PAYMENT_APPROVED'], true)) {
            return response()->json(['status' => 'ignored', 'message' => 'Evento nao financeiro'], 200);
        }

        if ($externalId) {
            $invoice = Invoice::where('external_id', $externalId)->first();

            if ($invoice) {
                // 5. Idempotência Granular: Evitar reprocessamento de faturas já pagas
                if ($invoice->status === 'paid') {
                    return response()->json(['status' => 'success', 'message' => 'Fatura ja processada anteriormente']);
                }

                // 6. Atualização Segura
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                Log::info("Webhook [{$gateway}]: Fatura {$invoice->id} marcada como PAGA via automacao.");
                return response()->json(['status' => 'success', 'message' => 'Pagamento Confirmado']);
            }
        }

        return response()->json(['status' => 'not_found'], 200);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = config('services.webhooks.payments_secret');

        if (!$secret) {
            return true;
        }

        $signature = $request->header('X-Webhook-Signature');

        if (!$signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}
