<?php

class DatabaseEventer
{
    // Метод, который будет вызываться при регистрации нового пользователя
    public static function OnRegisterNewUser()
    {
        $userId = Telegram::getUserId();
        $username = Telegram::getUserNickname();
        $registered = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `users` (`id`, `userId`, `username`, `registered`)
        SELECT 0, ?, ?, ?
        WHERE NOT EXISTS (
            SELECT 1 FROM `users` WHERE `userId` = ?
        )";

        $stmt = Database::prepare($sql);
        $stmt->bind_param('ssss', $userId, $username, $registered, $userId);
        $stmt->execute();
    }
} 
?>