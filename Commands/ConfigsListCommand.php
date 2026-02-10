<?php 
class ConfigsListCommand extends Command { 
    public function __construct(string $path) { 
        $this->setPath($path); 
    } 
 
    public function run() { 
        DatabaseEventer::ResetUserState();
        $userId = Telegram::getUserId();
        
        $configs = Database::fetchAll("SELECT c.id, c.configId, c.created_date, s.name as server_name 
                                       FROM configs c 
                                       JOIN servers s ON c.serverId = s.id 
                                       WHERE c.userId = '$userId' 
                                       ORDER BY c.created_date DESC");
        
        if (empty($configs)) {
            Telegram::sendMessage("📭 У вас пока нет созданных конфигураций.\n\nНажмите /new чтобы создать первую!");
            return;
        }
        
        $message = "📱 *Ваши конфигурации:*\n\n";
        $inlineKeyboard = ['inline_keyboard' => []];
        
        foreach ($configs as $index => $config) {
            $date = date('d.m.Y H:i', strtotime($config['created_date']));
            $message .= "🔒 *" . ($index + 1) . ". " . $config['server_name'] . "*\n";
            $message .= "   📅 Создана: " . $date . "\n";
            $message .= "   ─────────────────────\n";
            
            $inlineKeyboard['inline_keyboard'][] = [
                ['text' => '🗑️ Удалить ' . $config['server_name'], 'callback_data' => 'delete_config_' . $config['id']]
            ];
        }
        
        $message .= "\n💡 Нажмите на кнопку под сообщением, чтобы удалить конфигурацию.";
        
        Telegram::sendInlineMessage($message, $inlineKeyboard);
    } 
} 
?> 
