<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
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
        $envFilePath = getcwd().'/.env';
        $apiKey = 'API_KEY='.$this->argument('key');

        if (!File::exists($envFilePath)) {
            File::put($envFilePath, $apiKey);
        } else {
            File::append($envFilePath, PHP_EOL.$apiKey);
        }
    }
}
