# Changelog
**All changes** to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.6.0] - 2026-06-03

### Added
- **Gemini 3.5 Flash Support**: Added the new `gemini-3.5-flash` model (`GeminiModels::GEMINI_35_FLASH`).
- **Encapsulated Thinking Levels**: Added a `supportedThinkingLevels()` method directly inside the `GeminiModels` enum to cleanly manage supported levels per model.
- **Smart Default Thinking Option**: Fallback to the first supported thinking level for Gemini models if the `--thinking` option is active but no specific level is provided.

### Changed
- **Pure Plain Text Prompts**: Updated prompt guidelines in `Prompt.php` to strictly require pure plain-text output (forbidding any Markdown formatting, backticks, asterisks, bold text, or code blocks) and instructed the model to focus on the most significant change when multiple unrelated files/diffs are modified.
- **Centralized Default Model**: Defined `GeminiModels::DEFAULT_MODEL` (`gemini-2.5-flash-lite`) to standardize the default fallback across the application (`Commit.php` and `GoogleGemini.php`).
- **Simplified Token Estimation**: Refactored `getDefaultMaxTokens()` in `GoogleGemini.php` to dynamically check if a model supports thinking levels (or is `GEMINI_25_PRO`) to return `65536` max tokens, avoiding hardcoded model lists.
- **Thinking Mode Validation Refactoring**: Cleaned up the `getThinking()` method in the `Commit` command by delegating supported thinking configurations directly to the `GeminiModels` enum.
- **Stable Model Identifiers**: Promoted `GeminiModels::GEMINI_31_FLASH_LITE` from preview (`gemini-3.1-flash-lite-preview`) to stable (`gemini-3.1-flash-lite`).
- **Binary Rebuild**: Recompiled and updated the distributed binary build at `builds/xenogit`.

## [1.2.0] - 2023-08-07

### Changed
- Commit generation prompt

## [1.1.3] - 2023-08-07

### Fixed
- Update the commit message in the 'user' role to include the correctly formatted output of the 'git diff --staged' command. 

## [1.1.2] - 2023-08-07

### Changed
- Refactor `handle` method in `ConfigApiKey.php` to use file functions instead of File facade and add support for Phar binary directory.

## [1.1.1] - 2023-08-07

### Added
- `xenogit` bin for Packagist release

### Changed
- Excluded `builds/.env` file from version control in `.gitignore`
- Updated package name, description, keywords, homepage, and author email in `composer.json`

## [1.1.0] - 2023-08-07

### Added
- `ConfigApiKey.php`: Added command to configure API key in .env file.
- `Commit.php`: Added support for custom commit message input with Laravel Prompts.
- `xenogit`: Prepared to be installable via composer.

### Changed
- `Commit.php`: Removed unused private variable and moved OpenAI initialization to `generateCommitMessage` method.

### Removed
- `phpcs.xml`: Removed `phpcs.xml` configuration file.

### Dependencies
- Added `laravel/prompts` package as a dependency.

## [1.0.0] - 2023-08-06

### Added
- Xenogit logo in both dark and light versions
- PHPDoc comments for constructor, complete() method, and prepareData() method in OpenAI.php
- LICENSE.md file with MIT license
- README.md: Added initial content including project logo, badges, and getting started section
- Command class to automatically generate commit messages in Commit.php
- GPTModels enum and OpenAI class with constructor and properties in GPTModels.php and OpenAI.php
- .env.example file

### Changed
- README.md: Fixed double-spacing in Getting Started header
- README.md: Updated with installation, usage, API key, payment, contribute, and license information
- OpenAI.php: Updated and added support for API URL constant, use of Illuminate\Support\Facades\Http for HTTP requests, and updated model property to use GPTModels::GPT3_16K->value
- Commit.php: Refactored class for better readability and maintainability
- OpenAI.php and .env.example: Updated

### Removed
- InspireCommandTest.php file
- InspireCommand class and its associated methods and properties
- xenogit-alpha binary file

### Fixed
- Commit.php: Handled case when there are no changes yet and threw an exception

### Refactored
- Commit.php: Generated commit message using git diff
- OpenAI.php: Refactored OpenAI class

### Dependencies
- Added Guzzle HTTP client

### Miscellaneous
- Added phpcs.xml with PHP_CodeSniffer ruleset for xenogit project with PSR12 coding standard and specified files to be checked in .phpcs.xml
- Updated .gitignore to exclude .env file and added 'builds/' directory to ignore list in .gitignore
