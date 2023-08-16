<?php

namespace App;

use App\Enums\GPTModels;
use Illuminate\Support\Facades\Http;

class OpenAI
{
    protected const API_URL = 'https://api.openai.com/v1/chat/completions';

    /**
     * Constructor for the class.
     *
     * @param  string  $apiKey      the API key
     * @param  string  $model       the model to use (default: GPT3_16K)
     * @param  float  $temperature the temperature (default: 0)
     * @param  int  $maxTokens   the maximum number of tokens (default: 50)
     */
    public function __construct(
        protected string $apiKey,
        protected string $model = GPTModels::GPT3_16K->value,
        protected float $temperature = 0,
        protected int $maxTokens = 50
    ) {
    }

    /**
     * Sends a POST request to the OpenAI API to complete the given messages.
     *
     * @param  array  $messages the array of messages to be completed
     * @return string the completed message content
     *
     * @throws Exception if an error occurs during the HTTP request
     */
    public function complete(array $messages): string
    {
        $response = Http::withToken(
            token: $this->apiKey
        )->post(
            url: OpenAI::API_URL,
            data: $this->prepareData(messages: $messages)
        )->throw();

        return $response['choices'][0]['message']['content'];
    }

    /**
     * Prepares the data for the given messages.
     *
     * @param  array  $messages the array of messages
     * @return array the prepared data
     */
    protected function prepareData(array $messages): array
    {
        return [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ];
    }
}
