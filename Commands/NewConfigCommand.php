<?php 
class NewConfigCommand extends Command { 
    public function __construct(string $path) { 
        $this->setPath($path); 
    } 
 
    public function run() { 
        $servers = Database::fetchAll('SELECT id, name FROM servers WHERE visible = 1');
        $inlineKeyboard = [];
        foreach ($servers as $server) {
            $inlineKeyboard['inline_keyboard'][] = [
                ['text' => $server['name'], 'callback_data' => 'new_config_' . $server['id']],
            ];
        }
        Telegram::sendInlineMessage("Выберите страну для VPN:",$inlineKeyboard);
    } 
} 
?> 
