<?php 
class NewConfigCommand extends Command { 
    public function __construct(string $path) { 
        $this->setPath($path); 
    } 
 
    public function run() { 
        $inlineKeyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Создать конфигурацию', 'callback_data' => 'create_config'],
                    ['text' => 'Помощь', 'callback_data' => 'help'],
                ],
                [
                    ['text' => 'Настройки', 'callback_data' => 'settings'],
                    ['text' => 'О нас', 'callback_data' => 'about'],
                ],
            ],
        ];
        Telegram::sendInlineMessage("Выберите страну для VPN:",$inlineKeyboard);
    } 
} 
?> 
