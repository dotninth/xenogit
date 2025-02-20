<?php

namespace App;

use App\Enums\GeminiModels;
use Illuminate\Support\Facades\Http;

class GoogleGemini
{
    protected const API_URL = 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions';

    /**
     * The OpenAI API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * ID of the supported model to use.
     *
     *
     * @default GPT3_16K
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
     * Constructor for the class.
     *
     * @param  string  $apiKey  the API key
     * @param  string|null  $model  the model to use (default: GPT3_16K)
     * @param  float  $temperature  the temperature (default: 0)
     * @param  int  $maxTokens  the maximum number of tokens (default: 50)
     */
    public function __construct(
        string $apiKey,
        ?GeminiModels $model = null,
        ?float $temperature = null,
        ?int $maxTokens = null
    ) {
        $this->apiKey = $apiKey;
        $this->model = $model ?: GeminiModels::GEMINI_20_FLASH;
        $this->temperature = $temperature ?: 0;
        $this->maxTokens = $maxTokens ?: 100;
    }

    /**
     * Sends a POST request to the OpenAI API to complete the given messages.
     *
     * @param  array  $messages  the array of messages to be completed
     * @return string the completed message content
     *
     * @throws Exception if an error occurs during the HTTP request
     */
    public function complete(array $messages): string
    {
        $response = Http::withToken(
            token: $this->apiKey
        )->post(
            url: GoogleGemini::API_URL,
            data: $this->prepareData(messages: $messages)
        )->throw();

        return $response['choices'][0]['message']['content'];
    }

    /**
     * Prepares the data for the given messages.
     *
     * @param  array  $messages  the array of messages
     * @return array the prepared data
     */
    protected function prepareData(array $messages): array
    {
        return [
            'model' => $this->model->value,
            'messages' => $messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'top_p' => 1,
        ];
    }
}
