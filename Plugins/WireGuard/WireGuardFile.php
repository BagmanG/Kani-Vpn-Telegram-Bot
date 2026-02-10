<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require 'WireGuardAPI.php';
$token = $_GET['token'];
$serverPort = $_GET['server_port'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Ошибка: Некорректный ID клиента";
    exit;
}

try {
    $apiClient = new WireGuardAPI('http://'.$serverPort, $token);
    $clientConf = $apiClient->getClientById($id, 'conf');
    
    // Проверяем, не вернул ли API ошибку
    $responseData = json_decode($clientConf, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($responseData['error'])) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Ошибка: " . $responseData['error'];
        exit;
    }
    
    // Если ответ не начинается с [Interface], это тоже ошибка
    if (strpos(trim($clientConf), '[Interface]') !== 0) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Ошибка: Не удалось получить конфигурацию. Возможно, клиент был удален.";
        exit;
    }
    
    header('Content-Type: text/plain');
    header('Content-Disposition: inline; filename="vpn.conf"');
    echo $clientConf;
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Ошибка сервера: " . $e->getMessage();
}
?>