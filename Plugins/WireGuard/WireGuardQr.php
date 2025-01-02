<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require 'WireGuardAPI.php';
$token = $_GET['token'];
$server = $_GET['server'];
$id = $_GET['id'];
$apiClient = new WireGuardAPI('http://'.$server, $token);
header('Content-Type: image/png');
$clientConf = $apiClient->getClientById($id, 'qr');
echo $clientConf;
?>