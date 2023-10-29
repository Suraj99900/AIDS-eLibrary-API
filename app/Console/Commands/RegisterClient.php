<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\Client;

class RegisterClient extends Command
{
    protected $signature = 'register-client {--name= : Name of the client} {--callback= : Callback url of client}';

    protected $description = 'Register client';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if(!$this->option('name')){
            throw new \Exception("Please provide name");
        }

        if(!$this->option('callback')){
            throw new \Exception("Please provide callback url");
        }
        
        $model = (new Client)->registerClient([
            'name' => $this->option('name'),
            'callback_url' => $this->option('callback'),
        ]);

        $this->info("Registered client name: ".$model->name);
        $this->info("Registered client ID: ".$model->client_id);
        $this->info("Registered client Secret: ".$model->client_secret);
    }
}