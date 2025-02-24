<?php

namespace App;

use Exception;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Prompt
{
    public const SYSTEM_MESSAGE = <<<'EOD'
        You are to act as the author of a commit message in git. Your task is to create a clean and comprehensive commit message using conventional commit conventions. I'll send you the output of a 'git diff --staged' command, and you will convert it into a commit message.

        Reminders about the git diff format:
        For every file, there are a few metadata lines, like (for example):
        ```
        diff --git a/lib/index.js b/lib/index.js
        index aadf691..bfef603 100644
        --- a/lib/index.js
        +++ b/lib/index.js
        ```
        This means that `lib/index.js` was modified in this commit. Note that this is only an example.
        Then there is a specifier of the lines that were modified.
        A line starting with `+` means it was added.
        A line that starting with `-` means that line was deleted.
        A line that starts with neither `+` nor `-` is code given for context and better understanding.
        It is not part of the diff.

        Do not preface the commit with anything. Use the present tense. Don't add any descriptions to the commit, just the commit message. Commit should be only one line. Ideally, the message should be no longer than 74 characters, but that's not necessary. Reply in English.
    EOD;

    public static function getPrompt(): array
    {
        return [
            [
                'role' => 'system',
                'content' => self::SYSTEM_MESSAGE,
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
                'content' => self::getGitDiff(),
            ],
        ];
    }

    /**
     * Retrieves the git diff of the staged changes.
     *
     * @return string the git diff output
     *
     * @throws ProcessFailedException if the git diff command fails
     * @throws \Exception if there are no staged changes
     */
    private static function getGitDiff(): string
    {
        $process = new Process(['git', 'diff', '--staged']);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        if (empty($output)) {
            throw new Exception('There are no changes yet!');
        }

        return $output;
    }
}
