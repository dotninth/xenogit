# Changelog
**All changes** to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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