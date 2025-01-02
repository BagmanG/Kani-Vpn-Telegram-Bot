<?
class WireGuardManager{
    public static function CreateNewConfig($serverId){
        $server = Database::fetch("SELECT ip,port,api_key FROM servers WHERE id = $serverId");

        $api = new WireGuardAPI('http://'.$server['ip'].':'.$server['port'], $server['api_key']);
        $json = $api->createClient();
        $data = json_decode($json, true);
        $configId = $data['id'];
        Telegram::sendMessageFromCallback($json."____".$configId);
    }
}
?>