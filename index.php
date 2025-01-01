<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'autoload.php';

Core::Init();

$telegram = new Telegram($_ENV['TG_BOT_API']);

$telegram->addCommand(new StartCommand($telegram, "/start"));
$telegram->addCommand(new HelpCommand($telegram, "/help"));
$telegram->addCommand(new TestCommand($telegram, "/test"));

$telegram->run();
?>