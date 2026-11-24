<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Phar;

class ConfigApiKey extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config:api-key
        {key : The Gemini API key (required)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Configure the API key in the .env file';

    /**
     * Appends the supplied `API_KEY` entry to the `.env` beside the running PHAR. Creates the file if it does not exist.
     *
     * @return void
     */
    public function handle()
    {
        $binaryDir = Phar::running(false);
        $envFilePath = pathinfo($binaryDir, PATHINFO_DIRNAME) . '/.env';
        $apiKey = 'API_KEY=' . $this->argument('key');

        if (! file_exists($envFilePath)) {
            touch($envFilePath);
        }

        file_put_contents($envFilePath, PHP_EOL . $apiKey, FILE_APPEND);
    }
}
