<?php

class Telegram
{
    private static $botApiKey;
    private static $commands = [];
    private static $data;

    public static function init($botApiKey)
    {
        if (empty($botApiKey)) {
            throw new InvalidArgumentException("API_KEY is required.");
        }
        self::$botApiKey = $botApiKey;
    }

    public static function getApiKey(): string
    {
        return self::$botApiKey;
    }

    public static function addCommand(Command $command)
    {
        self::$commands[] = $command;
    }

    public static function runCommands()
    {
        foreach (self::$commands as $command) {
            $command->run();
        }
    }

    public static function run()
    {
        self::$data = file_get_contents('php://input');
        self::$data = json_decode(self::$data, true);
        self::sendMessage("Пришел запрос:".print_r(self::$data, true));
        // if (isset(self::$data['callback_query'])) {
        //     $callbackQuery = self::$data['callback_query'];
        //     $chatId = $callbackQuery['message']['chat']['id'];
        //     $callbackData = $callbackQuery['data'];
        //     self::sendMessage("Выбран ".$callbackData);
        // }
        if (empty(self::$data['message']['chat']['id'])) {
            exit();
        }
        if (!empty(self::$data['message']['text'])) {
            $text = self::$data['message']['text'];
            foreach (self::$commands as $command) {
                if ($command->getPath() == $text) {
                    $command->run();
                    return;
                }
            }
            self::sendMessage("Я тебя не понял:(");
        }
    }

    private static function sendTelegram($method, $response)
    {
        $ch = curl_init('https://api.telegram.org/bot' . self::$botApiKey . '/' . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    public static function sendMessage($message)
    {
        self::sendTelegram(
            'sendMessage',
            array(
                'chat_id' => self::$data['message']['chat']['id'],
                'text' => $message,
            )
        );
    }

    public static function sendInlineMessage($message, $inlineKeyboard)
    {
        self::sendTelegram(
            'sendMessage',
            array(
                'chat_id' => self::$data['message']['chat']['id'],
                'text' => $message,
                'reply_markup' => json_encode($inlineKeyboard),
            )
        );
    }

    public static function getUserId(): string
    {
        return self::$data['message']['from']['id'];
    }

    public static function getUserNickname(): string
    {
        return isset(self::$data['message']['from']['username']) ? self::$data['message']['from']['username'] : null;
    }
}
?>