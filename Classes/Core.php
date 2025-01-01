<?php
class Core
{
    private static $env;
    private const ENV_FILE_PATH = __DIR__ . '/../.env';

    public static function Init()
    {
        if (self::$env === null) {
            self::$env = new Environment(self::ENV_FILE_PATH);
        }
        try {
            self::$env->Load();
            Database::connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
        } catch (Exception $e) {
            die("Ошибка: " . $e->getMessage());
        }
    }

    public static function Close(){
        Database::close();
        self::$env = null;
    }
}