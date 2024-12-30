<?php
// Получаем входящие данные
$payload = file_get_contents('php://input');

// Декодируем JSON
$data = json_decode($payload, true);

// Проверяем, что это событие push
if (isset($data['ref']) && $data['ref'] === 'refs/heads/production') { // Замените 'main' на вашу ветку
    writeToLog('Пришел пуш в продакшн');
} else {
    writeToLog('Пришел пуш в '.$data['ref']);
    echo "Не push событие или ветка не совпадает.";
}




function writeToLog($message, $logFile = 'app.log') {
    // Убедитесь, что файл доступен для записи
    if (!is_writable($logFile)) {
        // Если файл не доступен для записи, можно создать его
        file_put_contents($logFile, '', LOCK_EX);
    }

    // Форматируем сообщение с меткой времени
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;

    // Записываем сообщение в лог-файл
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

?>