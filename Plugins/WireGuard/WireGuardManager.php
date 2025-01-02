<?
class WireGuardManager{
    public static function CreateNewConfig($serverId){
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
        $serverId = $server['id'];
        Database::execute("INSERT INTO `configs`(`id`, `userId`, `serverId`, `created_date`, `configId`) VALUES (0,'$userId',$serverId,'$createdDate',$configId)")
    }
}
?>