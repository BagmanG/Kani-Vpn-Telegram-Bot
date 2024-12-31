<?php

class Telegram
{
    private $botApiKey;
    private $commands = [];
    private $data;
    public function __construct($botApiKey)
    {
        if (empty($botApiKey)) {
            throw new InvalidArgumentException("API_KEY is required.");
        }
        $this->botApiKey = $botApiKey;
    }

    public function getApiKey(): string
    {
        return $this->botApiKey;
    }

    public function addCommand(Command $command)
    {
        $this->commands[] = $command;
    }

    //Test
    public function runCommands()
    {
        foreach ($this->commands as $command) {
            $command->Run();
        }
    }

    public function run()
    {
        $this->data = file_get_contents('php://input');
        $this->data = json_decode($this->data, true);

        if (empty($this->data['message']['chat']['id'])) {
            exit();
        }
        if (!empty($this->data['message']['text'])) {
            $text = $this->data['message']['text'];
            foreach ($this->commands as $command) {
                if ($command->getPath() == $text) {
                    $command->run();
                    return;
                }
            }
        }
    }

    //Telegram Методы

    private function sendTelegram($method, $response)
    {
        $ch = curl_init('https://api.telegram.org/bot' . $this->botApiKey . '/' . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    public function sendMessage($message)
    {
        $this->sendTelegram(
			'sendMessage', 
			array(
				'chat_id' => $this->data['message']['chat']['id'],
				'text' => $message,
			)
		);
    }
}