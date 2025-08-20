<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LocalAiService
{
    protected string $host;
    protected string $model;
    protected int $timeout;
    protected int $connectTimeout;

    public function __construct(?string $host = null, ?string $model = null)
    {
        $this->host = $host ?: env('OLLAMA_HOST', 'http://localhost:11434');
        // Default to Mistral; can be overridden via .env OLLAMA_MODEL
        $this->model = $model ?: env('OLLAMA_MODEL', 'mistral');
        // Allow long generations and cold starts; configurable
        $this->timeout = (int) env('OLLAMA_TIMEOUT', 120); // seconds
        $this->connectTimeout = (int) env('OLLAMA_CONNECT_TIMEOUT', 5); // seconds
    }

    public function generate(string $prompt, array $options = []): array
    {
        $payload = array_merge([
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
        ], $options);

        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->post(rtrim($this->host, '/').'/api/generate', $payload);
            if (!$response->ok()) {
                return ['ok' => false, 'error' => 'LLM HTTP '. $response->status().': '.$response->body()];
            }
            $data = $response->json();
            return ['ok' => true, 'text' => $data['response'] ?? ''];
        } catch (\Throwable $e) {
            $hint = 'Vérifiez qu\'Ollama tourne sur '.$this->host.' et que le modèle "'.$this->model.'" est disponible (ollama pull '.$this->model.'). Vous pouvez augmenter OLLAMA_TIMEOUT dans .env.';
            return ['ok' => false, 'error' => $e->getMessage().' — '.$hint];
        }
    }
}
