<?php 
class TestCommand extends Command { 
    public function __construct(string $path) { 
        $this->setPath($path); 
    } 
 
    public function run() { 
        //DatabaseEventer::OnRegisterNewUser();
        //Telegram::sendMessage("Test : ");
        WireGuardManager::GetConfigFile();
    }
}
?> 
