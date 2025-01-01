<?
class StartCommand extends Command {
    public function __construct(string $path) {
        $this->setPath($path);
    }

    public function run() {
        Telegram::sendMessage("Привет,мир и Булат!");
    }
}
?>