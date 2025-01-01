<?php

class Environment
{
    private $envFilePath;

    public function __construct($filePath)
    {
        $this->envFilePath = $filePath;
    }

    public function Load()
    {
        if (!file_exists($this->envFilePath)) {
            throw new Exception("Файл .env не найден: " . $this->envFilePath);
        }

        $lines = file($this->envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            $_ENV[$key] = $value;
        }
    }
}