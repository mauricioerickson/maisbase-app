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
        // 1. Logar o payload bruto para auditoria
        WebhookLog::create([
            'gateway' => $gateway,
            'payload' => $request->all(),
        ]);

        // 2. Localizar a Invoice (simulando estrutura do Asaas: $request->event e $request->payment->id)
        // Aqui deve-se adaptar para o gateway real escolhido.
        $externalId = $request->input('payment.id') ?? $request->input('id');

        if ($externalId) {
            $invoice = Invoice::where('external_id', $externalId)->first();

            if ($invoice) {
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
}
