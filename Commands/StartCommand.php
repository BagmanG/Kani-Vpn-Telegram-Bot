<?
class StartCommand extends Command {
    public function __construct(string $path) {
        $this->setPath($path);
    }

    public function run() {
        DatabaseEventer::OnRegisterNewUser();
        DatabaseEventer::ResetUserState();
        
        $welcomeMessage = "🎉 Добро пожаловать!\n\n" .
            "KaniVPN — ваш надежный помощник в создании VPN конфигураций. " .
            "Здесь вы сможете легко создавать конфигурации для любых ваших целей.\n\n" .
            "📱 ИНСТРУКЦИЯ ПО УСТАНОВКЕ:\n\n" .
            "iOS:\n" .
            "1️⃣ Скачайте приложение WireGuard из App Store\n" .
            "2️⃣ Откройте приложение и нажмите '+'\n" .
            "3️⃣ Выберите 'Создать из QR-кода' и отсканируйте QR код\n" .
            "4️⃣ Включите VPN переключателем\n\n" .
            "Android:\n" .
            "1️⃣ Скачайте WireGuard из Google Play\n" .
            "2️⃣ Нажмите '+' → 'Сканировать из QR-кода'\n" .
            "3️⃣ Отсканируйте QR код\n" .
            "4️⃣ Активируйте подключение\n\n" .
            "Windows/macOS/Linux:\n" .
            "1️⃣ Скачайте WireGuard с wireguard.com\n" .
            "2️⃣ Установите приложение\n" .
            "3️⃣ Нажмите 'Import tunnel(s) from file'\n" .
            "4️⃣ Выберите скачанный .conf файл\n" .
            "5️⃣ Нажмите 'Activate'\n\n" .
            "⚡️ ПОЛЕЗНЫЕ КОМАНДЫ:\n" .
            "/new - Создать VPN конфигурацию\n" .
            "/vpns - Список ваших конфигураций\n" .
            "/help - Помощь и поддержка\n\n" .
            "💡 ВАЖНО: У каждого пользователя может быть максимум 2 активные конфигурации.\n\n" .
            "Приятного пользования! 🚀";
        
        Telegram::sendPhotoWithCaption($_ENV['SERVER_ROOT']."/Images/start.jpg", $welcomeMessage);
    }
}
?>
