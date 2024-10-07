<?php

namespace App\Enums;

enum GPTModels: string
{
    case GPT3 = 'gpt-3.5-turbo';
    case GPT4 = 'gpt-4';
    case GPT4_TURBO = 'gpt-4-turbo';
    case GPT4_O = 'gpt-4o';
    case GPT4_O_MINI = 'gpt-4o-mini';
    case GPT4_O1 = 'o1-preview';
    case GPT4_O1_MINI = 'o1-mini';
}
