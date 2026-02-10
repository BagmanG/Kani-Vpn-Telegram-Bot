<?
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require 'WireGuardAPI.php';
$token = $_GET['token'];
$serverPort = $_GET['server_port'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/plain; charset=utf-8');
    die("❌ Ошибка: Некорректный ID клиента (ID: $id)\n\n⚠️ Возможные причины:\n1. Конфигурация не была создана успешно\n2. Исчерпан пул IP адресов на сервере\n3. Техническая ошибка при создании конфигурации\n\nПопробуйте создать новую конфигурацию или обратитесь в поддержку.");
}

try {
    $apiClient = new WireGuardAPI('http://'.$serverPort, $token);
    $clientConf = $apiClient->getClientById($id, 'conf');
    
    // Проверяем что получили конфигурацию
    if (empty($clientConf) || strpos($clientConf, '[Interface]') === false) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: text/plain; charset=utf-8');
        die("❌ Ошибка: Не удалось получить конфигурацию с сервера\n\nПопробуйте позже или обратитесь в поддержку.");
    }
    
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: inline; filename="vpn.conf"');
    echo $clientConf;
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/plain; charset=utf-8');
    die("❌ Ошибка при получении конфигурации: " . $e->getMessage() . "\n\nОбратитесь в поддержку.");
}
?>
