<?php

namespace App\Enums;

enum GPTModels: string
{
    case GPT3 = 'gpt-3.5-turbo';
    case GPT3_16K = 'gpt-3.5-turbo-16k';
    case GPT4 = 'gpt-4';
    case GPT4_32K = 'gpt-4-32k';
}
