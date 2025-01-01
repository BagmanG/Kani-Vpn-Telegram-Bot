<?php 
class HelpCommand extends Command { 
    public function __construct(Telegram $telegram, string $path) { 
        parent::__construct($telegram); 
        $this->setPath($path); 
    } 
 
    public function run() { 
        $this->telegram->sendMessage("Чем тебе помочь? ".$_ENV['DB_HOST']);
    } 
} 
?> 
