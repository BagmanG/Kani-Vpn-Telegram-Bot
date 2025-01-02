<?php 
class ConfigsListCommand extends Command { 
    public function __construct(string $path) { 
        $this->setPath($path); 
    } 
 
    public function run() { 
        Telegram::sendMessage("Вот список ваших конфигураций:");
    } 
} 
?> 
