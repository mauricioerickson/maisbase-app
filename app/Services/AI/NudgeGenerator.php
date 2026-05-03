<?php

// filepath: app/Services/AI/NudgeGenerator.php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Motor de Inteligência Artificial para geração de Nudges personalizados.
 * Utiliza o Google Gemini 1.5 Flash.
 */
class NudgeGenerator
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Gera uma mensagem personalizada baseada no contexto.
     */
    public function generate($context)
    {
        $prompt = $this->buildPrompt($context);

        if (!$this->apiKey) {
            Log::warning("Gemini API Key não configurada. Usando fallback.");
            return $this->getFallbackMessage($context);
        }

        try {
            $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? $this->getFallbackMessage($context);

        } catch (\Exception $e) {
            Log::error("Erro no Gemini AI: " . $e->getMessage());
            return $this->getFallbackMessage($context);
        }
    }

    /**
     * Monta o prompt estruturado para a IA.
     */
    protected function buildPrompt($context)
    {
        $tone = $context['tone'] ?? 'amigável';
        
        return "Você é o assistente virtual da escola de futebol {$context['arena_name']}. 
                Escreva uma mensagem no WhatsApp para o pai/mãe {$context['guardian_name']} sobre o atleta {$context['athlete_name']}. 
                Assunto: {$context['subject']}. 
                Contexto extra: {$context['extra']}.
                Tom da mensagem: {$tone}.
                REGRAS: 
                1. Seja breve e use emojis de futebol. 
                2. Não use placeholders como [NOME]. 
                3. Gere variações criativas para evitar SPAM.";
    }

    /**
     * Mensagem de fallback caso a IA falhe.
     */
    protected function getFallbackMessage($context)
    {
        return "Olá {$context['guardian_name']}, tudo bem? Passando para lembrar sobre {$context['subject']} do {$context['athlete_name']}. Abraços da equipe {$context['arena_name']}! ⚽";
    }
}
