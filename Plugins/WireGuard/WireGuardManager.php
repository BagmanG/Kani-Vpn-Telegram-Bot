<?php
class WireGuardManager
{
    const MAX_CONFIGS_PER_USER = 2;

    public static function CreateNewConfig($serverId)
    {
        $userId = Telegram::getUserId();
        
        // Проверяем количество существующих конфигураций
        $existingConfigs = Database::fetchAll("SELECT id, configId, serverId FROM configs WHERE userId = '$userId'");
        $configCount = count($existingConfigs);
        
        // Если у пользователя уже есть MAX_CONFIGS_PER_USER или больше конфигураций
        if ($configCount >= self::MAX_CONFIGS_PER_USER) {
            // Удаляем самую старую конфигурацию
            $oldestConfig = $existingConfigs[0];
            
            // Удаляем конфигурацию из WireGuard API
            $serverData = Database::fetch("SELECT ip, port, api_key FROM servers WHERE id = ".$oldestConfig['serverId']);
            if ($serverData) {
                $api = new WireGuardAPI('http://'.$serverData['ip'].':'.$serverData['port'], $serverData['api_key']);
                try {
                    $api->deleteClientById($oldestConfig['configId']);
                } catch (Exception $e) {
                    Logger::Rec("Ошибка удаления конфига из API: " . $e->getMessage());
                }
            }
            
            // Удаляем из базы данных
            Database::execute("DELETE FROM configs WHERE id = ".$oldestConfig['id']);
            
            Telegram::sendMessage("⚠️ Ваша самая старая конфигурация была автоматически удалена, так как у вас достигнут лимит в ".self::MAX_CONFIGS_PER_USER." конфигурации.");
        }
        
        // Создаем новую конфигурацию
        $server = Database::fetch("SELECT id, ip, port, api_key FROM servers WHERE id = $serverId");
        
        if (!$server) {
            Telegram::sendMessage("❌ Ошибка: сервер не найден.");
            return;
        }
        
        $api = new WireGuardAPI('http://'.$server['ip'].':'.$server['port'], $server['api_key']);
        
        try {
            $json = $api->createClient();
            $data = json_decode($json, true);
            
            // Проверяем корректность ответа от API
            if (!isset($data['id']) || $data['id'] <= 0) {
                Telegram::sendMessage("❌ Ошибка создания конфигурации: сервер не смог выделить IP адрес.\n\n⚠️ Возможно, исчерпан пул доступных IP адресов на сервере. Попробуйте выбрать другой сервер или обратитесь в поддержку.");
                Logger::Rec("API вернул невалидный ID: " . print_r($data, true));
                return;
            }
            
            $configId = $data['id'];
            $createdDate = date('Y-m-d H:i:s');
            
            // Сохраняем в базу данных
            $configIndex = Database::queryWithIndex("INSERT INTO `configs`(`id`, `userId`, `serverId`, `created_date`, `configId`) VALUES (0,'$userId',$serverId,'$createdDate',$configId)");
            
            if ($configIndex <= 0) {
                Telegram::sendMessage("❌ Ошибка сохранения конфигурации в базу данных.");
                // Пытаемся удалить созданного клиента из WireGuard
                try {
                    $api->deleteClientById($configId);
                } catch (Exception $e) {
                    Logger::Rec("Ошибка отката создания клиента: " . $e->getMessage());
                }
                return;
            }
            
            Telegram::sendMessage("✅ Ваша конфигурация успешно создана!\n\n📊 У вас сейчас активных конфигураций: " . min($configCount + 1, self::MAX_CONFIGS_PER_USER) . "/" . self::MAX_CONFIGS_PER_USER); 
            
            self::GetConfigQrCode($configIndex);
            self::GetConfigFile($configIndex);
            
        } catch (Exception $e) {
            Telegram::sendMessage("❌ Ошибка при создании конфигурации: " . $e->getMessage() . "\n\nПопробуйте позже или обратитесь в поддержку.");
            Logger::Rec("Исключение при создании конфига: " . $e->getMessage());
        }
    }

    public static function CheckBeforeCreate($serverId)
    {
        $userId = Telegram::getUserId();
        
        // Проверяем количество существующих конфигураций
        $existingConfigs = Database::fetchAll("SELECT id, created_date FROM configs WHERE userId = '$userId' ORDER BY created_date ASC");
        $configCount = count($existingConfigs);
        
        // Если у пользователя уже есть MAX_CONFIGS_PER_USER или больше конфигураций
        if ($configCount >= self::MAX_CONFIGS_PER_USER) {
            $oldestConfig = $existingConfigs[0];
            $createdDate = date('d.m.Y H:i', strtotime($oldestConfig['created_date']));
            
            // Создаем inline кнопки для подтверждения
            $inlineKeyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Да, создать новую', 'callback_data' => 'confirm_new_config_' . $serverId],
                        ['text' => '❌ Отмена', 'callback_data' => 'cancel_new_config']
                    ]
                ]
            ];
            
            Telegram::sendInlineMessage(
                "⚠️ У вас уже есть максимальное количество конфигураций ($configCount/".self::MAX_CONFIGS_PER_USER.").\n\n" .
                "Для создания новой конфигурации будет автоматически удалена самая старая конфигурация (создана: $createdDate).\n\n" .
                "Продолжить?",
                $inlineKeyboard
            );
            return false;
        }
        
        // Если меньше лимита - создаем сразу
        return true;
    }

    public static function GetConfigQrCode($configIndex)
    {
        $configData = Database::fetch("SELECT serverId, configId FROM configs WHERE id = $configIndex");
        
        if (!$configData) {
            Telegram::sendMessage("❌ Конфигурация не найдена.");
            return;
        }
        
        $configId = $configData['configId'];
        $serverId = $configData['serverId'];
        $serverData = Database::fetch("SELECT id, ip, port, api_key FROM servers WHERE id = $serverId");
        
        if (!$serverData) {
            Telegram::sendMessage("❌ Сервер не найден.");
            return;
        }
        
        $imageUrl = $_ENV['SERVER_ROOT']."Plugins/WireGuard/WireGuardQr.php?server_port=".$serverData['ip'].":".$serverData['port']."&token=".$serverData['api_key']."&id=".$configId;
        Telegram::sendPhotoWithCaption($imageUrl, "📱 QR код конфигурации");
    }

    public static function GetConfigFile($configIndex)
    {
        $configData = Database::fetch("SELECT serverId, configId FROM configs WHERE id = $configIndex");
        
        if (!$configData) {
            Telegram::sendMessage("❌ Конфигурация не найдена.");
            return;
        }
        
        $configId = $configData['configId'];
        $serverId = $configData['serverId'];
        $serverData = Database::fetch("SELECT id, ip, port, api_key FROM servers WHERE id = $serverId");
        
        if (!$serverData) {
            Telegram::sendMessage("❌ Сервер не найден.");
            return;
        }
        
        $fileUrl = $_ENV['SERVER_ROOT']."Plugins/WireGuard/WireGuardFile.php?server_port=".$serverData['ip'].":".$serverData['port']."&token=".$serverData['api_key']."&id=".$configId;
        Telegram::sendConfig($fileUrl, "📄 Файл конфигурации");
    }
}
?>
