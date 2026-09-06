<?php

namespace App\Enums;

enum GeminiModels: string
{
    case GEMINI_25_FLASH = 'gemini-2.5-flash';
    case GEMINI_25_FLASH_LITE = 'gemini-2.5-flash-lite';
    case GEMINI_25_PRO = 'gemini-2.5-pro';
    case GEMINI_3_FLASH = 'gemini-3-flash-preview';
    case GEMINI_31_PRO = 'gemini-3.1-pro-preview';
    case GEMINI_31_FLASH_LITE = 'gemini-3.1-flash-lite';
    case GEMINI_35_FLASH = 'gemini-3.5-flash';
    case GEMINI_35_FLASH_LITE = 'gemini-3.5-flash-lite';
    case GEMINI_36_FLASH = 'gemini-3.6-flash';
    case GEMINI_37_FLASH = 'gemini-3.7-flash';
    case GEMINI_38_FLASH = 'gemini-3.8-flash';
    public const DEFAULT_MODEL = self::GEMINI_25_FLASH_LITE;

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
            if ($case->value === $value || str_starts_with($case->value, "{$value}-")) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Get the supported thinking levels for the model.
     *
     * @return array<string> Supported thinking levels.
     */
    public function supportedThinkingLevels(): array
    {
        return match ($this) {
            self::GEMINI_3_FLASH => ['MINIMAL', 'LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_31_PRO => ['LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_31_FLASH_LITE => ['MINIMAL', 'LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_35_FLASH => ['MINIMAL', 'LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_35_FLASH_LITE => ['MINIMAL', 'LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_36_FLASH => ['MINIMAL', 'LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_37_FLASH => ['LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_38_FLASH => ['LOW', 'MEDIUM', 'HIGH'],
            default => [],
        };
    }
}
