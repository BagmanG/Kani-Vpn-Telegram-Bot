<?
$directories = [
    __DIR__ . '/Classes', // Классы
    __DIR__ . '/Commands', // Комманды
];

spl_autoload_register(function ($class_name) use ($directories) {
    $file_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
    foreach ($directories as $directory) {
        $file = $directory . DIRECTORY_SEPARATOR . $file_name;
        if (file_exists($file)) {
            include_once $file;
            return;
        }
    }
});
?>