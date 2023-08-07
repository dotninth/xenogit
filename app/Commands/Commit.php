<?php

namespace App\Commands;

use App\OpenAI;
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
    protected $signature = 'commit';

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
            $diff = $this->getGitDiff();
            $message = $this->generateCommitMessage($diff);
            $this->handleUserResponse($message);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Retrieves the git diff of the staged changes.
     *
     * @return string the git diff output
     *
     * @throws ProcessFailedException if the git diff command fails
     * @throws \Exception             if there are no staged changes
     */
    private function getGitDiff(): string
    {
        $process = new Process(['git', 'diff', '--staged']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        if (empty($output)) {
            throw new \Exception('There are no changes yet!');
        }

        return $output;
    }

    /**
     * Generate a commit message based on the output of a git diff command.
     *
     * @param string $diff the output of a git diff command
     *
     * @return string the generated commit message
     */
    private function generateCommitMessage(string $diff): string
    {
        if (env('API_KEY') === null) {
            throw new \Exception('API_KEY is not set!');
        }

        $openAi = new OpenAI(env('API_KEY'));

        return $openAi->complete([
            [
                'role' => 'system',
                'content' => "You are to act as the author of a commit message in git. Your task is to create a clean and comprehensive commit message using conventional commit conventions. I'll send you the output of a 'git diff --staged' command, and you will convert it into a commit message. Do not preface the commit with anything, use the present tense. Don't add any descriptions to the commit, just the commit message. The first line should be no longer than 50 characters, and the body should be limited to 72 characters. Reply in English.",
            ],
            [
                'role' => 'user',
                'content' => file_get_contents(__DIR__.'/example.diff'),
            ],
            [
                'role' => 'assistant',
                'content' => 'feat(server.ts): add support for process.env.PORT environment variable and change port variable case from lowercase port to uppercase PORT',
            ],
            [
                'role' => 'user',
                'content' => $diff,
            ],
        ]);
    }

    /**
     * Handles the user response and performs the necessary actions.
     *
     * @param string $message the commit response message
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
     * @param string $message the commit response message
     */
    private function shouldModifyCommit(string $message): bool
    {
        return $this->confirm('Do you want to modify it?');
    }

    /**
     * Prompts the user for a new commit message.
     *
     * @param string $message the commit response message
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
     * @param string $message the commit message
     *
     * @throws ProcessFailedException if the process of committing is not successful
     */
    private function commitChanges(string $message): void
    {
        $command = ['git', 'commit', '-m', $message];
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
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
