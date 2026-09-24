<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiClient
{
    private string $key;
    private string $model;

    public function __construct()
    {
        $this->key   = config('services.gemini.key');
        $this->model = config('services.gemini.model');
    }

    public function generate(string $systemInstruction, string $userMessage): string
    {
        if (empty($this->key)) {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->key}";

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $userMessage]]],
            ],
            'generationConfig' => [
                'temperature'     => 0.4,
                'maxOutputTokens' => 800,
            ],
        ];

        $response = Http::timeout(45)->retry(2, 2000, function ($exception, $request) {
            return $exception instanceof \Illuminate\Http\Client\ConnectionException
                || ($exception instanceof \Illuminate\Http\Client\RequestException
                    && $exception->response->status() === 503);
        })->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->status() === 429) {
                throw new RuntimeException('Too many requests, please wait a moment and try again.');
            }

            if ($response->status() === 503) {
                throw new RuntimeException('The AI assistant is busy right now, please try again in a moment.');
            }

            throw new RuntimeException('The AI assistant is temporarily unavailable.');
        }

        $finishReason = $response->json('candidates.0.finishReason');
        $text         = $response->json('candidates.0.content.parts.0.text');

        if (! $text) {
            Log::warning('Gemini empty response', [
                'finishReason' => $finishReason,
                'raw'          => $response->json(),
            ]);

            if ($finishReason === 'SAFETY') {
                throw new RuntimeException('I cannot answer that question. Please try rephrasing it.');
            }

            throw new RuntimeException('The AI assistant returned an empty response. Please try again.');
        }

        return trim($text);
    }
}
