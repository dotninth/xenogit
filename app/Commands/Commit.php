<?php

namespace App\Commands;

use App\Enums\GeminiModels;
use App\GoogleGemini;
use App\Prompt;
use Exception;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\textarea;

class Commit extends Command
{
    /**
     * Constant representing the action to modify the commit message.
     *
     * @var string
     */
    protected const MODIFY_COMMIT = 'modify_commit';

    /**
     * Constant representing the action to accept the commit message.
     *
     * @var string
     */
    protected const ACCEPT_COMMIT = 'accept_commit';

    /**
     * Constant representing the action to regenerate the commit message.
     *
     * @var string
     */
    protected const REGENERATE_COMMIT = 'regenerate_commit';

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'commit
                            {--m|model= : Set the ID of the model to use (optional). Default: gemini-2.5-flash-lite}
                            {--t|temperature= : Set the temperature (optional). Default: 0.3}
                            {--k|tokens= : Set the maximum number of tokens to use (optional). Default: 100}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Automatically generate commit messages';

    /**
     * The Gemini model to use for generating commit messages.
     */
    protected ?GeminiModels $model;

    /**
     * The temperature setting for the AI model.
     * Higher values make output more random, lower values more deterministic.
     */
    protected ?float $temperature;

    /**
     * The maximum number of tokens to generate in the response.
     */
    protected ?int $maxTokens;

    /**
     * The generated commit message.
     */
    protected string $message;

    /**
     * Handles the logic for the Command.
     */
    public function handle(): void
    {
        try {
            $this->setCommandOptions();
            $this->generateCommitMessage();
            $this->handleUserResponse();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Gets the ID of the GPT model from the command line option.
     *
     * @return GeminiModels|null the ID of the model to use
     *
     * @throws Exception if the model is not supported
     */
    private function getModel(): ?GeminiModels
    {
        $modelOption = $this->option('model');

        if ($modelOption === null) {
            return null;
        }

        $model = GeminiModels::tryFromCliFlag($modelOption);

        if ($model === null) {
            $supportedModels = implode("\n\t", GeminiModels::casesForCli());
            throw new Exception("Wrong model option!\nCurrently supported models are:\n\t{$supportedModels}");
        }

        return $model;
    }

    /**
     * Retrieves the temperature from the command line option.
     *
     * @return float|null The temperature to use, null if the option is not set
     *
     * @throws Exception If the temperature is not a positive float between 0 and 2
     **/
    private function getTemperature(): ?float
    {
        $temperatureOption = $this->option('temperature');

        if ($temperatureOption === null) {
            return null;
        }

        $temperature = (float) $temperatureOption;

        if (! is_float($temperatureOption) || $temperature < 0 || $temperature > 2) {
            throw new Exception('Temperature must be a positive float between 0 and 2!');
        }

        return $temperature;
    }

    /**
     * Gets the maximum number of tokens from the command line option.
     *
     * @return int|null the maximum number of tokens to use, null if the option is not set
     *
     * @throws Exception if the maximum number of tokens is not a positive integer
     **/
    private function getMaxTokens(): ?int
    {
        $maxTokensOption = $this->option('tokens');

        if ($maxTokensOption === null) {
            return null;
        }

        $maxTokens = (int) $maxTokensOption;

        if ($maxTokens <= 0) {
            throw new Exception('Maximum number of tokens must be a positive integer more than 0!');
        }

        return $maxTokens;
    }

    /**
     * Sets the command options.
     **/
    private function setCommandOptions(): void
    {
        $this->model = $this->getModel();
        $this->temperature = $this->getTemperature();
        $this->maxTokens = $this->getMaxTokens();
    }

    /**
     * Generate a commit message based on the output of a git diff command.
     */
    private function generateCommitMessage(): void
    {
        if (config('gemini.api_key') === null) {
            throw new Exception('API_KEY is not set!');
        }

        $googleGemini = new GoogleGemini(
            apiKey: config('gemini.api_key'),
            model: $this->model,
            temperature: $this->temperature,
            maxTokens: $this->maxTokens
        );

        $this->message = spin(
            message: 'Generating your commit message...',
            callback: fn () => $googleGemini->generate(messages: Prompt::getPrompt())
        );
    }

    /**
     * Handles the user response and performs the necessary actions.
     *
     * @throws ProcessFailedException if the process of committing is not successful
     */
    private function handleUserResponse(): void
    {
        $this->warn($this->message);

        $action = $this->askWhatToDoWithCommit();

        if ($action === Commit::REGENERATE_COMMIT) {
            $this->generateCommitMessage();
            $this->handleUserResponse();

            return;
        }

        if ($action === Commit::MODIFY_COMMIT) {
            $this->getNewCommitMessage();
            $this->warn($this->message);
        }

        if ($this->confirmCommit()) {
            $this->commitChanges();

            return;
        }

        $this->discardCommit();
    }

    /**
     * Checks if the user wants to modify the commit message.
     *
     * @return string The action to take with the commit message.
     */
    private function askWhatToDoWithCommit(): string
    {
        return select(
            label: 'Do you want to modify it?',
            options: [
                Commit::MODIFY_COMMIT => 'Yes',
                Commit::ACCEPT_COMMIT => 'No',
                Commit::REGENERATE_COMMIT => 'Regenerate',
            ],
            default: Commit::ACCEPT_COMMIT
        );
    }

    /**
     * Prompts the user for a new commit message.
     */
    private function getNewCommitMessage(): void
    {
        $this->message = textarea(
            label: 'Please enter the new commit message.',
            required: 'Commit message is required',
            default: $this->message,
            validate: fn (string $value) => match (true) {
                strlen($value) < 6 => 'Your commit should be longer than 6 characters.',
                default => null
            }
        );
    }

    /**
     * Confirms if the user accepts the commit message.
     */
    private function confirmCommit(): bool
    {
        return confirm(
            label: 'Do you accept this commit message?',
            default: true
        );
    }

    /**
     * Commits the changes using the given message.
     *
     * @throws ProcessFailedException if the process of committing is not successful
     */
    private function commitChanges(): void
    {
        $command = ['git', 'commit', '-m', $this->message];
        $process = new Process($command);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info('Commit successful!');
    }

    /**
     * Discards the commit message.
     */
    private function discardCommit(): void
    {
        $this->info('Commit message discarded.');
    }
}
