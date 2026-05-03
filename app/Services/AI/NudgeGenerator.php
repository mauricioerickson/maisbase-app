<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Motor de Inteligencia Artificial para geracao de nudges personalizados.
 * Utiliza o Google Gemini 1.5 Flash quando a chave esta configurada.
 */
class NudgeGenerator
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function generate($context)
    {
        $context = $this->normalizeContext($context);
        $prompt = $this->buildPrompt($context);

        if (!$this->apiKey) {
            Log::warning('Gemini API Key nao configurada. Usando fallback.');

            return $this->getFallbackMessage($context);
        }

        try {
            $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

            $data = $response->json();

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? $this->getFallbackMessage($context);
        } catch (\Exception $e) {
            Log::error('Erro no Gemini AI: ' . $e->getMessage());

            return $this->getFallbackMessage($context);
        }
    }

    protected function buildPrompt($context)
    {
        $tone = $context['tone'] ?? 'amigavel';

        return "Voce e o assistente virtual da escola de futebol {$context['arena_name']}.
                Escreva uma mensagem no WhatsApp para o pai/mae {$context['guardian_name']} sobre o atleta {$context['athlete_name']}.
                Assunto: {$context['subject']}.
                Contexto extra: {$context['extra']}.
                Tom da mensagem: {$tone}.
                REGRAS:
                1. Seja breve, humano e use no maximo um emoji de futebol.
                2. Nao use placeholders como [NOME].
                3. Inclua naturalmente o nome do atleta, o vencimento quando existir e o nome da escola.
                4. Varie abertura, ordem das frases e chamada final para evitar padrao repetitivo de WhatsApp.
                5. Evite tom ameacador; prefira ajuda, organizacao e proximo passo claro.";
    }

    protected function getFallbackMessage($context)
    {
        $templates = [
            'Oi, {guardian_name}. Tudo certo? A {arena_name} lembra que {athlete_name} tem {subject}. Se precisar, o PIX esta no portal.',
            '{guardian_name}, passando rapidinho pela {arena_name}: {subject} de {athlete_name}. Qualquer duvida, estamos por aqui.',
            'Bom dia, {guardian_name}. Para manter tudo organizado na {arena_name}, fica o lembrete de {subject} do {athlete_name}.',
            'Ola, {guardian_name}. A mensalidade do {athlete_name} esta no radar da {arena_name}: {subject}. Obrigado pela parceria.',
            '{guardian_name}, tudo bem? A {arena_name} separou este lembrete sobre {subject} do {athlete_name}.',
            'Oi, {guardian_name}. So para facilitar a rotina: {athlete_name} tem {subject} na {arena_name}.',
            '{guardian_name}, aqui e a equipe {arena_name}. Lembrete amigavel: {subject} relacionado ao {athlete_name}.',
            'Tudo bem, {guardian_name}? A {arena_name} esta conferindo as mensalidades e viu {subject} do {athlete_name}.',
            'Oi, {guardian_name}. Para o {athlete_name} seguir com tudo em dia na {arena_name}, fica o aviso: {subject}.',
            '{guardian_name}, mensagem rapida da {arena_name}: {subject} do {athlete_name}. Obrigado por cuidar disso com a gente.',
        ];

        $index = abs(crc32(($context['guardian_name'] ?? '') . ($context['athlete_name'] ?? '') . ($context['subject'] ?? ''))) % count($templates);
        $message = $templates[$index];

        foreach (['guardian_name', 'arena_name', 'athlete_name', 'subject'] as $key) {
            $message = str_replace("{{$key}}", $context[$key] ?? '', $message);
        }

        return $message;
    }

    private function normalizeContext(array $context): array
    {
        foreach (['arena_name', 'extra', 'subject'] as $key) {
            if (isset($context[$key])) {
                $context[$key] = str_replace('Rio Preto', 'Sao Jose do Rio Preto', $context[$key]);
                $context[$key] = str_replace('São José do Rio Preto', 'Sao Jose do Rio Preto', $context[$key]);
            }
        }

        return $context;
    }
}
