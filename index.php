<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

define("BOT_API_KEY","8013304224:AAEIb0K8rrTKxL0NIZyQGN4gZO6B_MA8CSM");

require_once 'Classes/Telegram.php';
require_once 'Classes/Command.php';
require_once 'Commands/StartCommand.php';

$telegram = new Telegram(BOT_API_KEY);

$startCommand = new StartCommand($telegram, "/start");

$telegram->addCommand($startCommand);

$telegram->run();
?>