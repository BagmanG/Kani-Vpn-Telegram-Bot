<?
abstract class Command {
    protected $telegram;
    protected $path;

    public function __construct(Telegram $telegram) {
        $this->telegram = $telegram;
    }
    abstract public function run();

    public function getPath(): string {
        return $this->path;
    }

    protected function setPath(string $path) {
        $this->path = $path;
    }
}
?>