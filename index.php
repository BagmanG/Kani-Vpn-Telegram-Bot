<?
define("BOT_API_KEY","8013304224:AAEIb0K8rrTKxL0NIZyQGN4gZO6B_MA8CSM");

require_once 'ApiClient.php';

$telegram = new Telegram(BOT_API_KEY);

$startCommand = new StartCommand($telegram, "/start");

$telegram->addCommand($startCommand);

$telegram->run();
?>