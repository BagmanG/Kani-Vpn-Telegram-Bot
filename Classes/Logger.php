<?
class Logger {
    public static function Rec($message, $logFile = 'logs.txt') {
        if (!is_writable($logFile)) {
            file_put_contents($logFile, '', LOCK_EX);
        }
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}
?>