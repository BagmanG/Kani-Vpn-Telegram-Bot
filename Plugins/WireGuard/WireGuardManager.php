<?
class WireGuardManager{
    public static function CreateNewConfig($serverId){
        $server = Database::fetch("SELECT ip FROM servers WHERE id = $serverId");
        Telegram::sendMessageFromCallback("IP:".$server['ip']);
    }
}
?>