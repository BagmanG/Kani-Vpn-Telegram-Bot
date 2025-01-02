<?
class WireGuardManager
{
    public static function CreateNewConfig($serverId)
    {
        $server = Database::fetch("SELECT id,ip,port,api_key FROM servers WHERE id = $serverId");

        $api = new WireGuardAPI('http://'.$server['ip'].':'.$server['port'], $server['api_key']);
        $json = $api->createClient();
        $data = json_decode($json, true);
        $configId = $data['id'];
        $userId = Telegram::getUserId();
        $createdDate = date('Y-m-d H:i:s');
        Telegram::sendMessage("Ваша конфигурация успешно создана!");
        $configIndex = Database::queryWithIndex("INSERT INTO `configs`(`id`, `userId`, `serverId`, `created_date`, `configId`) VALUES (0,'$userId',$serverId,'$createdDate',$configId)");
        self::GetConfigQrCode($configIndex);
        self::GetConfigFile($configIndex);
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