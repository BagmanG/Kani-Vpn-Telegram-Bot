<?
class WireGuardManager
{
    public static function CreateNewConfig($serverId)
    {
        $server = Database::fetch("SELECT id,ip,port,api_key FROM servers WHERE id = $serverId");

        /*$api = new WireGuardAPI('http://'.$server['ip'].':'.$server['port'], $server['api_key']);
        $json = $api->createClient();
        $data = json_decode($json, true);
        $configId = $data['id'];
        Telegram::sendMessageFromCallback($json."____".$configId);
        */
        $configId = 4;
        $userId = Telegram::getUserId();
        $createdDate = date('Y-m-d H:i:s');
        Database::execute("INSERT INTO `configs`(`id`, `userId`, `serverId`, `created_date`, `configId`) VALUES (0,'$userId',$serverId,'$createdDate',$configId)");
    }

    public static function GetConfigQrCode()
    {
        $configIndex = 1;
        $configData = Database::fetch("SELECT serverId,configId FROM configs WHERE id = $configIndex");
        $configId = $configData['configId'];
        $serverId = $configData['serverId'];
        $serverData = Database::fetch("SELECT id,ip,port,api_key FROM servers WHERE id = $serverId");
        $imageUrl = $_ENV['SERVER_ROOT']."Plugins/WireGuard/WireGuardQr.php?server_port=".$serverData['ip'].":".$serverData['port']."&token=".$serverData['api_key']."&id=".$configId;
        Telegram::sendPhotoWithCaption($imageUrl,"Вы успешно создали конфигурацию.");
    }
    public static function GetConfigFile()
    {
        $configIndex = 1;
        $configData = Database::fetch("SELECT serverId,configId FROM configs WHERE id = $configIndex");
        $configId = $configData['configId'];
        $serverId = $configData['serverId'];
        $serverData = Database::fetch("SELECT id,ip,port,api_key FROM servers WHERE id = $serverId");
        $fileUrl = $_ENV['SERVER_ROOT']."Plugins/WireGuard/WireGuardFile.php?server_port=".$serverData['ip'].":".$serverData['port']."&token=".$serverData['api_key']."&id=".$configId;
        Telegram::sendMessage("Test");
        Telegram::sendDocument($fileUrl,"Файл конфигурации.");
    }
}
?>