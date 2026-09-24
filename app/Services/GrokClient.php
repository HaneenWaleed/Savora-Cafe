<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GrokClient
{
    private const ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    private string $key;
    private string $model;

    public function __construct()
    {
        $this->key   = (string) config('services.groq.key');
        $this->model = (string) config('services.groq.model');
    }

    public function generate(string $systemInstruction, string $userMessage): string
    {
        $payload = $this->buildPayload($systemInstruction, $userMessage, [
            'temperature' => 0.4,
            'max_tokens'  => 1500,
        ]);

        $response = $this->send($payload, retryOnServerBusy: true);

        if ($response->failed()) {
            Log::error('Groq API error', [
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

        $text = $response->json('choices.0.message.content');

        if (! $text) {
            Log::warning('Groq empty response', [
                'finish_reason' => $response->json('choices.0.finish_reason'),
                'raw'           => $response->json(),
            ]);

            throw new RuntimeException('The AI assistant returned an empty response. Please try again.');
        }

        return trim($text);
    }

    public function generateJson(string $systemInstruction, string $userMessage): array
    {
        $payload = $this->buildPayload($systemInstruction, $userMessage, [
            'temperature'     => 0.3,
            'max_tokens'      => 2500,
            'response_format' => ['type' => 'json_object'],
        ]);

        $response = $this->send($payload, retryOnServerBusy: false);

        if ($response->failed()) {
            Log::error('Groq API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new RuntimeException('The AI assistant could not process this request. Please try rephrasing it.');
        }

        $text = $response->json('choices.0.message.content');

        if (! $text) {
            Log::warning('Groq empty JSON response', [
                'finish_reason' => $response->json('choices.0.finish_reason'),
            ]);

            throw new RuntimeException('The AI assistant returned an empty response.');
        }

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            Log::warning('Groq returned invalid JSON', ['raw' => $text]);

            throw new RuntimeException('The AI assistant returned an invalid response. Please try again.');
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function buildPayload(string $systemInstruction, string $userMessage, array $options): array
    {
        $payload = array_merge([
            'model'    => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemInstruction],
                ['role' => 'user', 'content' => $userMessage],
            ],
        ], $options);

        // Reasoning models (gpt-oss) spend max_tokens on hidden thinking; keep it short
        // so the actual answer/JSON is not cut off. Other models reject this parameter.
        if (str_contains($this->model, 'gpt-oss')) {
            $payload['reasoning_effort'] = 'low';
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function send(array $payload, bool $retryOnServerBusy)
    {
        if (empty($this->key)) {
            throw new RuntimeException('Groq API key is not configured.');
        }

        try {
            return Http::withToken($this->key)
                ->timeout(45)
                ->retry(2, 2000, function ($exception) use ($retryOnServerBusy) {
                    return $exception instanceof ConnectionException
                        || ($retryOnServerBusy
                            && $exception instanceof RequestException
                            && $exception->response->status() === 503);
                }, throw: false)
                ->post(self::ENDPOINT, $payload);
        } catch (RequestException $e) {
            Log::error('Groq API error (thrown)', [
                'status' => $e->response->status(),
                'body'   => $e->response->body(),
            ]);

            throw new RuntimeException('The AI assistant is temporarily unavailable.');
        } catch (ConnectionException $e) {
            Log::error('Groq connection error', ['message' => $e->getMessage()]);

            throw new RuntimeException('The AI assistant is temporarily unavailable.');
        }
    }
}
