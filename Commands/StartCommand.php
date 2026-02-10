<?
class StartCommand extends Command {
    public function __construct(string $path) {
        $this->setPath($path);
    }

    public function run() {
        DatabaseEventer::OnRegisterNewUser();
        DatabaseEventer::ResetUserState();
        Telegram::sendPhotoWithCaption($_ENV['SERVER_ROOT']."/Images/start.jpg","Добро пожаловать!\n\nKaniVPN — ваш надежный помощник в создании VPN конфигураций. Здесь вы сможете легко создавать конфигурации для любых ваших целей.\n\nВот пару команд, которые будут вам полезны:\n/new - Создать vpn конфигурацию\n/help - Помощь/Задать вопрос.\n\nПриятного пользования!");
    }
}
?>