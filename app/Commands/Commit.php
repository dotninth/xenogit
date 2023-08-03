<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Commit extends Command
{
    /**
     * The signature of the command.
     */
    protected $signature = 'commit';

    /**
     * The description of the command.
     */
    protected $description = 'Automatically generate commit messages';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $gitDiff = $this->getGitDiff();
            $commitMessage = $this->generateCommitMessage($gitDiff);
            $this->handleUserResponse($commitMessage);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the staged git diff.
     */
    private function getGitDiff(): string
    {
        $process = new Process(['git', 'diff', '--staged']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Generate the commit message using the OpenAI API.
     */
    private function generateCommitMessage(string $gitDiff): string
    {
        return 'result';
    }

    /**
     * Handle the user's response to the generated commit message.
     */
    private function handleUserResponse(string $commitMessage): void
    {
        $this->info('Here is the generated commit message:');
        $this->line($commitMessage);
        $this->newLine();

        if ($this->confirm('Do you want to modify it?')) {
            $commitMessage = $this->anticipate('Pease enter the new commit message. Use TAB to autocomplete', [$commitMessage]);
        }

        if ($this->confirm("Do you accept this commit message?\n{$commitMessage}\n", true)) {
            $process = new Process(['git', 'commit', '-m', $commitMessage]);
            $process->setTty(true);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->info('Commit successful!');
        } else {
            $this->info('Commit message discarded.');
        }
    }
}
