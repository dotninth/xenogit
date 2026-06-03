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
     * ID of the supported model to use.
     *
     *
     * @default GEMINI_25_FLASH_LITE
     */
    protected ?GeminiModels $model;

    /**
     * The temperature parameter.
     *
     *
     * @default 0
     */
    protected ?float $temperature;

    /**
     * The maximum number of tokens to generate in the completion.
     *
     * @default 50
     */
    protected ?int $maxTokens;

    /**
     * The thinking level for Gemini 3 models.
     */
    protected ?string $thinking;

    /**
     * Constructor for the class.
     *
     * @param  string  $apiKey  the API key
     * @param  GeminiModels|null  $model  the model to use (default: GEMINI_25_FLASH_LITE)
     * @param  float|null  $temperature  the temperature (default: 0.3)
     * @param  int|null  $maxTokens  the maximum number of tokens (default: 100)
     * @param  string|null  $thinking  the thinking level (default: null)
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
     * Sends a POST request to the Google Gemini API to generate new content.
     *
     * @param  array  $messages  the array of messages to be completed
     * @return string the completed message content
     *
     * @throws Exception if an error occurs during the HTTP request
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
     * Prepares the data for the given messages.
     *
     * @param  array  $messages  the array of messages
     * @return array the prepared data
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
            'temperature' => $this->temperature,
            'maxOutputTokens' => $this->maxTokens,
            'responseMimeType' => 'text/plain',
        ];

        if ($this->thinking) {
            $data['generationConfig']['thinkingConfig'] = [
                'thinkingLevel' => $this->thinking,
            ];
        }

        return $data;
    }

    /**
     * Returns the default value for the maximum number of tokens.
     *
     * @return int the default value for the maximum number of tokens
     */
    protected function getDefaultMaxTokens(): int
    {
        if ($this->model === GeminiModels::GEMINI_25_PRO || ! empty($this->model->supportedThinkingLevels())) {
            return 65536;
        }

        return 100;
    }
}
