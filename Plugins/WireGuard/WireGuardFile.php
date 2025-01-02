<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require 'WireGuardAPI.php';
$token = $_GET['token'];
$serverPort = $_GET['server_port'];
$id = $_GET['id'];
$apiClient = new WireGuardAPI('http://'.$serverPort, $token);
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="vpn.conf"');
$clientConf = $apiClient->getClientById($id, 'conf');
echo $clientConf;
?>