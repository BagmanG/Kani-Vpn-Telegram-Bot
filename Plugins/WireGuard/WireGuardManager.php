<?
class WireGuardManager
{
    const MAX_CONFIGS_PER_USER = 2;

    public static function CreateNewConfig($serverId)
    {
        $userId = Telegram::getUserId();
        
        // Проверяем количество существующих конфигураций пользователя
        $existingConfigs = Database::fetchAll("SELECT id, configId, serverId, created_date FROM configs WHERE userId = '$userId' ORDER BY created_date DESC");
        $configCount = count($existingConfigs);
        
        // Если уже есть максимум конфигураций, предлагаем удалить старые
        if ($configCount >= self::MAX_CONFIGS_PER_USER) {
            $inlineKeyboard = [
                'inline_keyboard' => [
                    [['text' => '✅ Да, удалить старые и создать новую', 'callback_data' => 'confirm_delete_old_' . $serverId]],
                    [['text' => '❌ Нет, отмена', 'callback_data' => 'cancel_delete']]
                ]
            ];
            
            $message = "⚠️ У вас уже есть " . $configCount . " конфигураций (максимум " . self::MAX_CONFIGS_PER_USER . ").\n\n";
            $message .= "Самые старые конфигурации будут удалены:\n";
            
            // Показываем только старые конфигурации (все кроме последних 2)
            $oldConfigs = array_slice($existingConfigs, self::MAX_CONFIGS_PER_USER - $configCount);
            foreach ($oldConfigs as $index => $config) {
                $server = Database::fetch("SELECT name FROM servers WHERE id = " . $config['serverId']);
                $serverName = $server['name'] ?? 'Unknown';
                $message .= "• " . $serverName . " (" . date('d.m.Y H:i', strtotime($config['created_date'])) . ")\n";
            }
            
            $message .= "\nХотите удалить их и создать новую конфигурацию?";
            
            Telegram::sendInlineMessage($message, $inlineKeyboard);
            DatabaseEventer::OnChangeUserState("waiting_delete_confirmation");
            return;
        }
        
        // Создаем новую конфигурацию
        self::createConfigAndSend($serverId);
    }
    
    public static function createConfigAndSend($serverId, $deleteOldConfigs = false)
    {
        $userId = Telegram::getUserId();
        
        // Если нужно удалить старые конфигурации
        if ($deleteOldConfigs) {
            $existingConfigs = Database::fetchAll("SELECT id, configId, serverId FROM configs WHERE userId = '$userId' ORDER BY created_date DESC");
            
            // Удаляем все кроме последних 2 (которые будут заменены)
            $configsToDelete = array_slice($existingConfigs, self::MAX_CONFIGS_PER_USER - count($existingConfigs));
            
            foreach ($configsToDelete as $config) {
                self::DeleteConfig($config['id'], $config['configId'], $config['serverId']);
            }
        }
        
        $server = Database::fetch("SELECT id,ip,port,api_key FROM servers WHERE id = $serverId");
        $api = new WireGuardAPI('http://'.$server['ip'].':'.$server['port'], $server['api_key']);
        $json = $api->createClient();
        $data = json_decode($json, true);
        
        // Проверяем, получили ли корректный ID
        if (!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])) {
            Telegram::sendMessage("❌ Не удалось создать конфигурацию. Ошибка получения ID от сервера.\n\nПопробуйте позже или выберите другой сервер.");
            return;
        }
        
        $configId = $data['id'];
        $createdDate = date('Y-m-d H:i:s');
        Telegram::sendMessage("✅ Ваша конфигурация успешно создана!"); 
        $configIndex = Database::queryWithIndex("INSERT INTO `configs`(`id`, `userId`, `serverId`, `created_date`, `configId`) VALUES (0,'$userId',$serverId,'$createdDate',$configId)");
        self::GetConfigQrCode($configIndex);
        self::GetConfigFile($configIndex);
    }
    
    public static function DeleteConfig($configIndex, $configId = null, $serverId = null)
    {
        // Если не переданы параметры, получаем их из БД
        if ($configId === null || $serverId === null) {
            $configData = Database::fetch("SELECT configId, serverId FROM configs WHERE id = $configIndex");
            if (!$configData) return;
            $configId = $configData['configId'];
            $serverId = $configData['serverId'];
        }
        
        // Удаляем из WireGuard API
        $serverData = Database::fetch("SELECT ip,port,api_key FROM servers WHERE id = $serverId");
        if ($serverData) {
            try {
                $api = new WireGuardAPI('http://'.$serverData['ip'].':'.$serverData['port'], $serverData['api_key']);
                $api->deleteClientById((int)$configId);
            } catch (Exception $e) {
                // Логируем ошибку, но продолжаем удаление из БД
                Logger::log("Error deleting config from API: " . $e->getMessage());
            }
        }
        
        // Удаляем из БД
        Database::execute("DELETE FROM configs WHERE id = $configIndex");
    }

    public static function GetConfigQrCode($configIndex)
    {
        $configData = Database::fetch("SELECT serverId,configId FROM configs WHERE id = $configIndex");
        $configId = $configData['configId'];
        $serverId = $configData['serverId'];
        $serverData = Database::fetch("SELECT id,ip,port,api_key FROM servers WHERE id = $serverId");
        $imageUrl = $_ENV['SERVER_ROOT']."Plugins/WireGuard/WireGuardQr.php?server_port=".$serverData['ip'].":".$serverData['port']."&token=".$serverData['api_key']."&id=".$configId;
        Telegram::sendPhotoWithCaption($imageUrl,"QR код конфигураци.");
    }
    public static function GetConfigFile($configIndex)
    {
        $configData = Database::fetch("SELECT serverId,configId FROM configs WHERE id = $configIndex");
        $configId = $configData['configId'];
        $serverId = $configData['serverId'];
        $serverData = Database::fetch("SELECT id,ip,port,api_key FROM servers WHERE id = $serverId");
        $fileUrl = $_ENV['SERVER_ROOT']."Plugins/WireGuard/WireGuardFile.php?server_port=".$serverData['ip'].":".$serverData['port']."&token=".$serverData['api_key']."&id=".$configId;
        Telegram::sendConfig($fileUrl,"Файл конфигурации.");
    }
}
?>