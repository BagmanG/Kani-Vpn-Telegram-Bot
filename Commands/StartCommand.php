<?
class StartCommand extends Command {
    public function __construct(Telegram $telegram, string $path) {
        parent::__construct($telegram);
        $this->setPath($path);
    }

    public function run() {
        $this->telegram->sendMessage("Привет,мир и Булат!!");
    }
}
?>