<?php

namespace App\Commands;

use App\OpenAI;
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

    private $openAi;

    public function __construct()
    {
        parent::__construct();
        $this->openAi = new OpenAI(env('API_KEY'));
    }

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
        return $this->openAi->complete([
            [
                'role' => 'system',
                'content' => "You are to act as the author of a commit message in git. Your task is to create a clean and comprehensive commit message using conventional commit conventions. I'\''ll send you the output of a '\''git diff --staged'\'' command, and you will convert it into a commit message. Do not preface the commit with anything, use the present tense. Don'\''t add any descriptions to the commit, just the commit message. The first line should be no longer than 50 characters, and the body should be limited to 72 characters. Reply in English.",
            ],
            [
                'role' => 'user',
                'content' => 'diff --git a/src/server.ts b/src/server.ts\n    index ad4db42..f3b18a9 100644\n    --- a/src/server.ts\n    +++ b/src/server.ts\n    @@ -10,7 +10,7 @@ import {\n      initWinstonLogger();\n      \n      const app = express();\n    -const port = 7799;\n    +const PORT = 7799;\n      \n      app.use(express.json());\n      \n    @@ -34,6 +34,6 @@ app.use((_, res, next) => {\n      // ROUTES\n      app.use(PROTECTED_ROUTER_URL, protectedRouter);\n      \n    -app.listen(port, () => {\n    -  console.log(\\`Server listening on port \\{$port}\\`);\n    +app.listen(process.env.PORT || PORT, () => {\n    +  console.log(\\`Server listening on port \\{$PORT}\\`);\n      });',
            ],
            [
                'role' => 'assistant',
                'content' => 'feat(server.ts): add support for process.env.PORT environment variable and change port variable case from lowercase port to uppercase PORT',
            ],
            [
                'role' => 'user',
                'content' => $gitDiff,
            ],
        ]);
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
