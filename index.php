<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'autoload.php';

Core::Init();

Telegram::init($_ENV['TG_BOT_API']);

Telegram::addCommand(new StartCommand("/start"));
Telegram::addCommand(new HelpCommand("/help"));
Telegram::addCommand(new TestCommand("от души!"));
Telegram::addCommand(new NewConfigCommand("/new"));
Telegram::addCommand(new ConfigsListCommand("/vpns"));
Telegram::addCommand(new FAQCommand("/faq"));
Telegram::run();

Core::Close();
?>