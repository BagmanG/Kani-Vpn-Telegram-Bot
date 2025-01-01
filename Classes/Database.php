<?php

class Database {
    private static $connection;

    public static function connect($host, $user, $pass, $db) {
        if (self::$connection === null) {
            self::$connection = new mysqli($host, $user, $pass, $db);

            // Проверка соединения
            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }
        }
    }

    public static function query($sql) {
        return self::$connection->query($sql);
    }

    public static function fetchAll($sql) {
        $result = self::query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function fetch($sql) {
        $result = self::query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    public static function execute($sql) {
        return self::query($sql);
    }

    public static function prepare($sql) {
        return self::$connection->prepare($sql);
    }

    public static function escape($string) {
        return self::$connection->real_escape_string($string);
    }

    public static function close() {
        if (self::$connection) {
            self::$connection->close();
            self::$connection = null; // Обнуляем соединение после закрытия
        }
    }
}
?>