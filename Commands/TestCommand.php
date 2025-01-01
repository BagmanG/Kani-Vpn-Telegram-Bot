<?php 
class TestCommand extends Command { 
    public function __construct(Telegram $telegram, string $path) { 
        parent::__construct($telegram); 
        $this->setPath($path); 
    } 
 
    public function run() { 
        //Database::execute("INSERT IGNORE INTO `users` (`id`, `userId`, `username`, `registered`) VALUES (0, '".$this->telegram->getUserId()."', '".$this->telegram->getUserNickname()."', '".date('Y-m-d H:i:s')."');");
        Database::execute("INSERT INTO `users` (`id`, `userId`, `username`, `registered`) SELECT 0, '".$this->telegram->getUserId()."', '".$this->telegram->getUserNickname()."', '".date('Y-m-d H:i:s')."' WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `userId` = '".$this->telegram->getUserId()."');");
        $this->telegram->sendMessage("Test : "- $this->telegram->getUserId() ."   ". $this->telegram->getUserNickname());
    } 
} 
?> 
