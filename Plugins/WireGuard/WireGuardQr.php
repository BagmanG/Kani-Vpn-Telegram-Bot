<?
require 'WireGuardAPI.php';
$token = $_GET['token'];
$server = $_GET['server'];
$id = 3;
$apiClient = new WireGuardAPI('http://'.$server, $token);
header('Content-Type: image/png');
$clientConf = $apiClient->getClientById($id, 'qr');
echo $clientConf;
?>