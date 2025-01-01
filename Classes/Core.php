<?php

class Core
{
    private $env;
    private const ENV_FILE_PATH = './'.__DIR__ . '/.env';

    public function __construct()
    {
        $this->env = new Environment(self::ENV_FILE_PATH);
    }

    public function Init()
    {
        try {
            $this->env->Load();
        } catch (Exception $e) {
            die("Ошибка: " . $e->getMessage());
        }
    }
}