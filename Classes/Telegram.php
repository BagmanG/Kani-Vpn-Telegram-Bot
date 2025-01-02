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
        $dataText = print_r(self::$data, true);
        Telegram::sendMessage($dataText);

        if(isset(self::$data['message']['chat']['id'])){
            //Если это беседа админов
            if(self::$data['message']['chat']['id']==-1002176982830){
                if(isset(self::$data['message']['reply_to_message'])){
                    $question = self::$data['message']['reply_to_message']['text'];
                    $answer = self::$data['message']['text'];
                    Telegram::sendMessage("Ответ отправлен пользователю!\n\nВопрос:$question\n\nОтвет: $answer");
                }
                return;
            }
        }

        //Если каллбек, то парсим
        if (isset(self::$data['callback_query'])) {
            Logger::Rec(self::$data);
            self::tryParseCallback(self::$data['callback_query']['data']);
            return;
        }
        if (empty(self::$data['message']['chat']['id'])) {
            exit();
        }
        //Если сообщение, то обрабатываем
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
                'chat_id' => self::getChatId(),
                'text' => $message,
            )
        );
    }

    public static function sendPhotoWithCaption($photoUrl, $caption = null)
    {
        $data = array(
            'chat_id' => self::getChatId(),
            'photo' => $photoUrl,
        );
    
        if (!empty($caption)) {
            $data['caption'] = $caption;
        }
    
        self::sendTelegram('sendPhoto', $data);
    }

    public static function sendConfig($filePath, $caption = null)
    {
        $data = array(
            'chat_id' => self::getChatId(),
            'document' => curl_file_create($filePath, 'text/plain' , 'vpn.conf'),
        );
        if ($caption) {
            $data['caption'] = $caption;
        }

        self::sendTelegram('sendDocument', $data);
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
        return isset(self::$data['message']['from']['id']) ? self::$data['message']['from']['id'] : self::$data['callback_query']['from']['id'];
    }

    public static function getUserNickname(): string
    {
        return isset(self::$data['message']['from']['username']) ? self::$data['message']['from']['username'] : null;
    }

    public static function getChatId(): string
    {
        return isset(self::$data['message']['chat']['id']) ? self::$data['message']['chat']['id'] : self::$data['callback_query']['message']['chat']['id'];
    }

    public static function tryParseCallback($callbackData)
    {
        //Если каллбек на создание конфига
        if (strpos($callbackData, 'new_config_') !== false) {
            preg_match('/new_config_(\d+)/', $callbackData, $matches);
            if (isset($matches[1])) {
                WireGuardManager::CreateNewConfig($matches[1]);
                return;
            }
        }
    }
}
?>