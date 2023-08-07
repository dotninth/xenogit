<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class ConfigApiKey extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config:api-key
        {key : The OpenAI API key (required)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Configure the API key in the .env file';

    /**
     * Handles the logic for the command.
     */
    public function handle()
    {
        $binaryDir = \Phar::running(false);
        $envFilePath = pathinfo($binaryDir, PATHINFO_DIRNAME).'/.env';
        $apiKey = 'API_KEY='.$this->argument('key');

        if (!file_exists($envFilePath)) {
            touch($envFilePath);
        }

        file_put_contents($envFilePath, PHP_EOL.$apiKey, FILE_APPEND);
    }
}
