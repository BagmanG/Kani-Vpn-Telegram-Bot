<?php 
class TestCommand extends Command { 
    public function __construct(Telegram $telegram, string $path) { 
        parent::__construct($telegram); 
        $this->setPath($path); 
    } 
 
    public function run() { 
        $this->telegram->sendMessage("Test : "- $this->telegram->getUserId() ."   ". $this->telegram->getUserNickname());
    } 
} 
?> 
