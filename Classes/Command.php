<?
abstract class Command {
    protected $path;
    abstract public function run();

    public function getPath(): string {
        return $this->path;
    }

    protected function setPath(string $path) {
        $this->path = $path;
    }
}
?>