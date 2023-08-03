<?php

namespace App;

use App\Enums\GPTModels;

class OpenAI
{
    public function __construct(
        protected string $apiKey,
        protected string $model = GPTModels::GPT3_16K,
        protected float $temperature = 0.2,
        protected int $maxTokens = 196
    ) {
    }
}
