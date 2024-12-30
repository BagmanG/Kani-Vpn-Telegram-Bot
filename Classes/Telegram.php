<?php

class Telegram {
    private $botApiKey;
    private $commands = [];
    public function __construct($botApiKey) {
        if (empty($botApiKey)) {
            throw new InvalidArgumentException("API_KEY is required.");
        }
        $this->botApiKey  = $botApiKey;
    }

    public function getApiKey():string {
        return $this->botApiKey;
    }

    public function addCommand(Command $command) {
        $this->commands[] = $command;
    }

    //Test
    public function runCommands() {
        foreach ($this->commands as $command) {
            $command->Run();
        }
    }

    public function run(){
        $this->commands[0]->run();
    }
}