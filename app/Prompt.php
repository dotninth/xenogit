<?php

namespace App;

use Exception;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Prompt
{
    public const SYSTEM_MESSAGE = <<<'EOD'
        # IDENTITY and PURPOSE

        You are an expert project manager and developer, and you specialize in creating super clean git messages using conventional commit conventions.

        I'll send you the output of a 'git diff --staged' command, and you will convert it into a commit message.

        ## Reminders about the git diff format:

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

        # STEPS

        - Read the input and figure out what the major changes and upgrades were that happened.
        - Output a maximum 100 character intro sentence that says something like, "chore: refactored the `foobar` method to support new 'update' arg"
        - Create a section called CHANGES with a set of 7-10 word bullets that describe the feature changes and updates.
        - Keep the number of bullets limited and succinct

        # OUTPUT INSTRUCTIONS

        - Use conventional commits - i.e. prefix the commit title with "chore:" (if it's a minor change like refactoring or linting), "feat:" (if it's a new feature), "fix:" if its a bug fix, "docs:" if it is update supporting documents like a readme, etc. 
        - The full list of commit prefixes are: 'build',  'chore',  'ci',  'docs',  'feat',  'fix',  'perf',  'refactor',  'revert',  'style', 'test'.
        - Your first line should follow this pattern: {type}({scope}): {subject}.
        - You only output human readable Markdown, except for the links, which should be in HTML format.
        - You do not format your commit as a block of code.
        - You only describe your changes in imperative mood, e.g. "make xyzzy do frotz" instead of "[This patch] makes xyzzy do frotz" or "[I] changed xyzzy to do frotz", as if you are giving orders to the codebase to change its behavior. Try to make sure your explanation can be understood without external resources.
        - You do not use the past tense, only the present tense.
        - You do not preface your commit with anything.
        - Reply in English.
    EOD;

    public const ASSISTANT_REPLY = <<<'EOD'
        feat(server.ts): configure server port from environment variable

        ## CHANGES
        - Refactor port variable to PORT for better constant naming convention.
        - Configure server to listen on environment port or default port.
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
                'content' => self::ASSISTANT_REPLY,
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
