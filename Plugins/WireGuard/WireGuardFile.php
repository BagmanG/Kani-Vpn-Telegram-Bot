<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require 'WireGuardAPI.php';
$token = $_GET['token'];
$serverPort = $_GET['server_port'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Некорректный ID клиента");
}
$apiClient = new WireGuardAPI('http://'.$serverPort, $token);
header('Content-Type: text/plain');
header('Content-Disposition: inline; filename="vpn.conf"');
$clientConf = $apiClient->getClientById($id, 'conf');
echo $clientConf;
?>