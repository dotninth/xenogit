<?php

namespace App;

use App\Enums\GPTModels;
use Illuminate\Support\Facades\Http;

class OpenAI
{
    protected const API_URL = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        protected string $apiKey,
        protected string $model = GPTModels::GPT3_16K->value,
        protected float $temperature = 0,
        protected int $maxTokens = 196
    ) {
    }

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
