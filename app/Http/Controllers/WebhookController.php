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
    /**
     * Processa o webhook do gateway (ex: Asaas, Mercado Pago).
     */
    public function handle(Request $request, $gateway)
    {
        if (!$this->hasValidSignature($request)) {
            Log::warning('Webhook de pagamento rejeitado por assinatura invalida.', [
                'gateway' => $gateway,
                'external_id' => $request->input('payment.id') ?? $request->input('id'),
            ]);

            return response()->json(['status' => 'unauthorized'], 401);
        }

        // 1. Logar o payload bruto para auditoria
        WebhookLog::create([
            'gateway' => $gateway,
            'payload' => $request->all(),
        ]);

        // 2. Localizar a Invoice (simulando estrutura do Asaas: $request->event e $request->payment->id)
        // Aqui deve-se adaptar para o gateway real escolhido.
        $externalId = $request->input('payment.id') ?? $request->input('id');
        $event = $request->input('event');

        if (!in_array($event, ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED', 'PAYMENT_APPROVED'], true)) {
            return response()->json(['status' => 'ignored', 'message' => 'Evento nao financeiro'], 200);
        }

        if ($externalId) {
            $invoice = Invoice::where('external_id', $externalId)->first();

            if ($invoice) {
                if ($invoice->status === 'paid') {
                    return response()->json(['status' => 'success', 'message' => 'Pagamento ja confirmado']);
                }

                // 3. Atualizar status se o evento for de pagamento recebido
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                return response()->json(['status' => 'success', 'message' => 'Pagamento Confirmado']);
            }
        }

        return response()->json(['status' => 'ignored'], 200);
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
