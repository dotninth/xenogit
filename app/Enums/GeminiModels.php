<?php

namespace App\Enums;

enum GeminiModels: string
{
    case GEMINI_20_FLASH = 'gemini-2.0-flash';
    case GEMINI_20_FLASH_LITE = 'gemini-2.0-flash-lite-preview-02-05';
    case GEMINI_20_FLASH_THINKING = 'gemini-2.0-flash-thinking-exp-01-21';
}
