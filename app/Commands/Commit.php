<?php

namespace App\Commands;

use App\Enums\GPTModels;
use App\OpenAI;
use App\Prompt;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\text;

class Commit extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'commit
                            {--m|model= : Set the ID of the model to use (optional). Default: gpt-3.5-turbo-16k}
                            {--t|temperature= : Set the temperature (optional). Default: 0}
                            {--k|tokens= : Set the maximum number of tokens to use (optional). Default: 50}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Automatically generate commit messages';

    /**
     * Handles the logic for the Command.
     */
    public function handle()
    {
        try {
            [$model, $temperature, $maxTokens] = $this->getCommandOptions();
            $type = $this->selectCommitType();
            $message = $this->generateCommitMessage($model, $temperature, $maxTokens, $type);
            $this->handleUserResponse($message);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Gets the ID of the GPT model from the command line option.
     *
     * @return GPTModels|null   the ID of the model to use
     *
     * @throws \Exception       if the model is not supported
     */
    private function getModel(): ?GPTModels
    {
        $modelOption = $this->option('model');

        if ($modelOption === null) {
            return null;
        }

        $model = GPTModels::tryFrom($modelOption);

        if ($model === null) {
            $supportedModels = implode(', ', array_column(GPTModels::cases(), 'value'));
            throw new \Exception('Wrong model option! Currently supported models are: ' . $supportedModels);
        }

        return $model;
    }

    /**
     * Retrieves the temperature from the command line option.
     *
     * @return float|null The temperature to use, null if the option is not set
     *
     * @throws \Exception If the temperature is not a positive float between 0 and 2
     **/
    private function getTemperature(): ?float
    {
        $temperatureOption = $this->option('temperature');

        if ($temperatureOption === null) {
            return null;
        }

        $temperature = (float) $temperatureOption;

        if (! is_float($temperatureOption) || $temperature < 0 || $temperature > 2) {
            throw new \Exception('Temperature must be a positive float between 0 and 2!');
        }

        return $temperature;
    }

    /**
     * Gets the maximum number of tokens from the command line option.
     *
     * @return int|null the maximum number of tokens to use, null if the option is not set
     *
     * @throws \Exception if the maximum number of tokens is not a positive integer
     **/
    private function getMaxTokens(): ?int
    {
        $maxTokensOption = $this->option('tokens');

        if ($maxTokensOption === null) {
            return null;
        }

        $maxTokens = (int) $maxTokensOption;

        if ($maxTokens <= 0) {
            throw new \Exception('Maximum number of tokens must be a positive integer more than 0!');
        }

        return $maxTokens;
    }

    /**
     * Gets the command options.
     *
     * @return array the command options
     **/
    private function getCommandOptions(): array
    {
        return [
            $this->getModel(),
            $this->getTemperature(),
            $this->getMaxTokens(),
        ];
    }

    /**
     * Returns the selected commit type by a user.
     *
     * @return string the selected commit type
     *
     */
    private function selectCommitType(): string
    {
        $types = [
            'feat',
            'fix',
            'docs',
            'refactor',
            'style',
            'chore',
            'build',
            'ci',
            'perf',
            'test'
        ];

        return $this->choice('Select preferred commit type', $types);
    }

    /**
     * Generate a commit message based on the output of a git diff command.
     *
     * @param  GPTModels|null  $model the ID of the supported model to use
     * @param  float|null  $temperature the temperature to use
     * @param  int|null  $maxTokens the maximum number of tokens to use
     * @param  string  $type the selected commit type
     * @return string the generated commit message
     */
    private function generateCommitMessage(?GPTModels $model, ?float $temperature, ?int $maxTokens, string $type): string
    {
        if (env('API_KEY') === null) {
            throw new \Exception('API_KEY is not set!');
        }

        $openAi = new OpenAI(env('API_KEY'), $model, $temperature, $maxTokens);

        return $openAi->complete(Prompt::getPrompt());
    }

    /**
     * Handles the user response and performs the necessary actions.
     *
     * @param  string  $message the commit response message
     *
     * @throws ProcessFailedException if the process of committing is not successful
     */
    private function handleUserResponse(string $message): void
    {
        $this->warn($message);

        if ($this->shouldModifyCommit($message)) {
            $message = $this->getNewCommitMessage($message);
            $this->warn($message);
        }

        if ($this->confirmCommit($message)) {
            $this->commitChanges($message);

            return;
        }

        $this->discardCommit();
    }

    /**
     * Checks if the user wants to modify the commit message.
     *
     * @param  string  $message the commit response message
     */
    private function shouldModifyCommit(string $message): bool
    {
        return $this->confirm('Do you want to modify it?');
    }

    /**
     * Prompts the user for a new commit message.
     *
     * @param  string  $message the commit response message
     */
    private function getNewCommitMessage(string $message): string
    {
        return text(
            label: 'Please enter the new commit message.',
            required: 'Commit message is required',
            default: $message
        );
    }

    /**
     * Confirms if the user accepts the commit message.
     */
    private function confirmCommit(): bool
    {
        $confirmationMessage = 'Do you accept this commit message?';

        return $this->confirm($confirmationMessage, true);
    }

    /**
     * Commits the changes using the given message.
     *
     * @param  string  $message the commit message
     *
     * @throws ProcessFailedException if the process of committing is not successful
     */
    private function commitChanges(string $message): void
    {
        $command = ['git', 'commit', '-m', $message];
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
