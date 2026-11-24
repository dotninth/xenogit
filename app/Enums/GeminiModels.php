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
    case GEMINI_4_CARBON = 'gemini-4-carbon';
    case GEMINI_4_NEON = 'gemini-4-neon';
    case GEMINI_4_HYDROGEN = 'gemini-4-hydrogen';
    case GEMINI_4_ARGON = 'gemini-4-argon';

    public const DEFAULT_MODEL = self::GEMINI_31_FLASH_LITE;

    /**
     * Lists CLI model IDs after stripping suffixes of the form `-word-NN-NN`.
     *
     * @return list<string> The model IDs in enum declaration order.
     */
    public static function casesForCli(): array
    {
        return array_map(
            fn (self $case) => preg_replace('/-\w+-\d{2}-\d{2}$/', '', $case->value),
            self::cases()
        );
    }

    /**
     * Matches a CLI model ID. Older models also accept prefixes that end at a hyphen boundary.
     *
     * @param  string  $value  The model ID supplied to `--model`.
     * @return self|null The first matching model in declaration order, or null if none matches.
     */
    public static function tryFromCliFlag(string $value): ?self
    {
        foreach (self::cases() as $case) {
            // Match exact value or value with version suffix
            if ($case->value === $value || (! $case->isGemini4() && str_starts_with($case->value, "{$value}-"))) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Checks the model ID for the `gemini-4-` prefix.
     *
     * @return bool Whether the model is in the Gemini 4 family.
     */
    public function isGemini4(): bool
    {
        return str_starts_with($this->value, 'gemini-4-');
    }

    /**
     * Lists the model's supported thinking levels, with the CLI default first.
     *
     * @return list<string> The uppercase thinking levels, or an empty list if the model has none.
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
            self::GEMINI_4_CARBON, self::GEMINI_4_ARGON => ['LOW', 'MEDIUM', 'HIGH'],
            self::GEMINI_4_NEON, self::GEMINI_4_HYDROGEN => ['LOW', 'MEDIUM', 'HIGH', 'XHIGH'],
            default => [],
        };
    }
}
