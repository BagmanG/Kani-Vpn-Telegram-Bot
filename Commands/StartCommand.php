<?
class StartCommand extends Command {
    public function __construct(string $path) {
        $this->setPath($path);
    }

    public function run() {
        Telegram::sendMessage("Добро пожаловать!\n\nKaniVPN — ваш надежный помощник в создании VPN конфигураций. Здесь вы сможете легко создавать конфигурации для любых ваших целей.\n\nВот пару команд, которые будут вам полезны:\n/new - Создать vpn конфигурацию\n/vpns - Список ваших vpn конфигураций\n/help - Помощь/Задать вопрос.\n\nПриятного пользования!");
    }
}
?>