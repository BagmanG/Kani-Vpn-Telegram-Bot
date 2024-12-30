<?
class StartCommand extends Command {
    public function __construct(Telegram $telegram, string $path) {
        parent::__construct($telegram);
        $this->setPath($path);
    }

    public function run() {
        echo "Start command executed at path: " . $this->getPath() . "\n";
        //$this->telegram->runCommands();
    }
}
?>