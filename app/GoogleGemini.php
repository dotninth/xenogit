<?php

namespace App;

use App\Enums\GeminiModels;
use Illuminate\Support\Facades\Http;

class GoogleGemini
{
    protected const API_BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models';

    protected const API_ACTION = 'generateContent';

    /**
     * The Gemini API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The model used in the request URL and to choose generation settings.
     *
     * @var GeminiModels|null
     */
    protected ?GeminiModels $model;

    /**
     * The sampling temperature. Requests for Gemini 4 omit this setting.
     *
     * @var float|null
     */
    protected ?float $temperature;

    /**
     * The output token limit sent in the generation settings.
     *
     * @var int|null
     */
    protected ?int $maxTokens;

    /**
     * The thinking level sent to Gemini, or null to omit the thinking configuration.
     *
     * @var string|null
     */
    protected ?string $thinking;

    /**
     * Stores credentials and generation settings. Resolves model-specific defaults before any request.
     *
     * @param  string  $apiKey  The Gemini API key used to authenticate requests.
     * @param  GeminiModels|null  $model  The model to call, or null to use `GeminiModels::DEFAULT_MODEL`.
     * @param  float|null  $temperature  The sampling temperature. Zero or null uses 0.3.
     * @param  int|null  $maxTokens  The output token limit. Zero or null uses the model's default.
     * @param  string|null  $thinking  The thinking level to send, or null to omit it.
     */
    public function __construct(
        string $apiKey,
        ?GeminiModels $model = null,
        ?float $temperature = null,
        ?int $maxTokens = null,
        ?string $thinking = null
    ) {
        $this->apiKey = $apiKey;
        $this->model = $model ?: GeminiModels::DEFAULT_MODEL;
        $this->temperature = $temperature ?: 0.3;
        $this->maxTokens = $maxTokens ?: $this->getDefaultMaxTokens();
        $this->thinking = $thinking;
    }

    /**
     * Posts the prompt to Gemini with a 180-second request timeout.
     *
     * @param  array<array{role: string, content: string}>  $messages  The system instructions and user prompt to send.
     * @return string The first text part of the first candidate, or an empty string if the field is absent.
     *
     * @throws \Illuminate\Http\Client\ConnectionException If the request cannot connect or times out.
     * @throws \Illuminate\Http\Client\RequestException If Gemini responds with a client or server error.
     */
    public function generate(array $messages): string
    {
        $url = sprintf(
            '%s/%s:%s?key=%s',
            self::API_BASE_URL,
            $this->model->value,
            self::API_ACTION,
            $this->apiKey
        );

        $response = Http::timeout(seconds: 180)->post(
            url: $url,
            data: $this->prepareData(messages: $messages)
        )->throw();

        return $response->json('candidates.0.content.parts.0.text', '');
    }

    /**
     * Builds the JSON request data. Uses the last `system` and `user` messages and ignores other roles.
     *
     * @param  array<array{role: string, content: string}>  $messages  The system instructions and user prompt.
     * @return array{
     *     system_instruction?: array{parts: list<array{text: string}>},
     *     contents?: list<array{parts: list<array{text: string}>}>,
     *     generationConfig: array{
     *         maxOutputTokens: int|null,
     *         responseMimeType: 'text/plain',
     *         temperature?: float|null,
     *         thinkingConfig?: array{thinkingLevel: string}
     *     }
     * } The generation settings and any system instructions or user content.
     */
    protected function prepareData(array $messages): array
    {
        $data = [];

        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $data['system_instruction'] = [
                    'parts' => [['text' => $message['content']]],
                ];
            } elseif ($message['role'] === 'user') {
                $data['contents'] = [
                    [
                        'parts' => [['text' => $message['content']]],
                    ],
                ];
            }
        }

        $data['generationConfig'] = [
            'maxOutputTokens' => $this->maxTokens,
            'responseMimeType' => 'text/plain',
        ];

        if (! $this->model->isGemini4()) {
            $data['generationConfig']['temperature'] = $this->temperature;
        }

        if ($this->thinking) {
            $data['generationConfig']['thinkingConfig'] = [
                'thinkingLevel' => $this->thinking,
            ];
        }

        return $data;
    }

    /**
     * Chooses the default output token limit for the configured model.
     *
     * @return int 32,768 for Carbon and Argon, 65,536 for Gemini 2.5 Pro and models with thinking levels other than Carbon and Argon, or 100 otherwise.
     */
    protected function getDefaultMaxTokens(): int
    {
        if (in_array($this->model, [GeminiModels::GEMINI_4_CARBON, GeminiModels::GEMINI_4_ARGON], true)) {
            return 32768;
        }

        if ($this->model === GeminiModels::GEMINI_25_PRO || ! empty($this->model->supportedThinkingLevels())) {
            return 65536;
        }

        return 100;
    }
}
