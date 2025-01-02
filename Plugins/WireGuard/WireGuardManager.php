<?
class WireGuardManager{
    public static function CreateNewConfig($serverId){
        $server = Database::fetch("SELECT ip,port,api_key FROM servers WHERE id = $serverId");
        Telegram::sendMessageFromCallback("IP:".$server['ip']."_".$server['port']."_".$server['api_key']);
    }
}
?>