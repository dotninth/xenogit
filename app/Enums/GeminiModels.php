<?php

namespace App\Enums;

enum GeminiModels: string
{
    case GEMINI_20_FLASH = 'gemini-2.0-flash';
    case GEMINI_20_FLASH_LITE = 'gemini-2.0-flash-lite';
    case GEMINI_25_FLASH = 'gemini-2.5-flash-preview-05-20';
    case GEMINI_25_PRO = 'gemini-2.5-pro-preview-05-06';

    /**
     * Get all cases without version suffix.
     *
     * @return array<string> All cases without version suffix.
     */
    public static function casesForCli(): array
    {
        return array_map(
            fn (self $case) => preg_replace('/-\w+-\d{2}-\d{2}$/', '', $case->value),
            self::cases()
        );
    }

    /**
     * Try to create a GeminiModels from a CLI flag value.
     *
     * @param  string  $value  CLI flag value.
     * @return GeminiModels|null GeminiModels or null if not found.
     */
    public static function tryFromCliFlag(string $value): ?self
    {
        foreach (self::cases() as $case) {
            // Match exact value or value with version suffix
            if ($case->value === $value || str_starts_with($case->value, "$value-")) {
                return $case;
            }
        }

        return null;
    }
}
