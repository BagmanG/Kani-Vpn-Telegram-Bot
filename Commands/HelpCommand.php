<?php
class HelpCommand extends Command
{
    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    public function run()
    {
        Telegram::sendMessage("Чем тебе помочь? " . $_ENV['DB_HOST']);
    }
} 
?>