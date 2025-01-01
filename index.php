<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'autoload.php';

Core::Init();

Telegram::init($_ENV['TG_BOT_API']);

Telegram::addCommand(new StartCommand($telegram, "/start"));
Telegram::addCommand(new HelpCommand($telegram, "/help"));
Telegram::addCommand(new TestCommand($telegram, "/test"));

Telegram::run();

Core::Close();
?>