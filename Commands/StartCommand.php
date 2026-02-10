<?
class StartCommand extends Command {
    public function __construct(string $path) {
        $this->setPath($path);
    }

    public function run() {
        DatabaseEventer::OnRegisterNewUser();
        DatabaseEventer::ResetUserState();
        
        $message = "🐉 *Добро пожаловать в KaniVPN!*\n\n";
        $message .= "KaniVPN — ваш надежный помощник в создании VPN конфигураций. Здесь вы сможете легко создавать конфигурации для любых ваших целей.\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 *Доступные команды:*\n";
        $message .= "• /new — Создать VPN конфигурацию\n";
        $message .= "• /vpns — Мои конфигурации\n";
        $message .= "• /help — Помощь / Задать вопрос\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📱 *Как установить VPN:*\n\n";
        $message .= "1️⃣ *Создайте конфигурацию:*\n";
        $message .= "   Нажмите /new и выберите сервер\n\n";
        $message .= "2️⃣ *Установите приложение:*\n";
        $message .= "   • iOS: WireGuard (App Store)\n";
        $message .= "   • Android: WireGuard (Google Play)\n";
        $message .= "   • Windows: WireGuard (официальный сайт)\n";
        $message .= "   • macOS: WireGuard (App Store / brew)\n";
        $message .= "   • Linux: `sudo apt install wireguard`\n\n";
        $message .= "3️⃣ *Импортируйте конфигурацию:*\n";
        $message .= "   • Откройте приложение WireGuard\n";
        $message .= "   • Нажмите + → *Импорт из файла*\n";
        $message .= "   • Выберите полученный файл .conf\n\n";
        $message .= "4️⃣ *Подключитесь:*\n";
        $message .= "   Нажмите кнопку включения в приложении\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💡 *Совет:* Вы можете использовать QR-код для быстрого импорта на мобильных устройствах!\n\n";
        $message .= "Приятного пользования! 🐉";
        
        Telegram::sendPhotoWithCaption($_ENV['SERVER_ROOT']."/Images/start.jpg", $message);
    }
}
?>