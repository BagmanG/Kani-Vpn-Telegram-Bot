@echo off
chcp 65001 > nul

setlocal enabledelayedexpansion

if "%~1"=="" (
    echo Укажите команду.
    exit /b
)

if "%~2"=="" (
    echo Укажите имя команды.
    exit /b
)

set "commandName=%~2"
set "fileName=Commands\%commandName%Command.php"

if not exist "Commands" (
    mkdir "Commands"
)

echo ^<?php > "!fileName!"
echo class %commandName%Command extends Command { >> "!fileName!"
echo     public function __construct(Telegram ^$telegram, string ^$path) { >> "!fileName!"
echo         parent::__construct(^$telegram); >> "!fileName!"
echo         ^$this-^>setPath(^$path); >> "!fileName!"
echo     } >> "!fileName!"
echo. >> "!fileName!"
echo     public function run() { >> "!fileName!"
echo         //... >> "!fileName!"
echo     } >> "!fileName!"
echo } >> "!fileName!"
echo ^?^> >> "!fileName!"

echo Файл "!fileName!" успешно создан.