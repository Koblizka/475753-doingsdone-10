<?php

// Данные для подключения к ДБ
$data_base = [
    "host" => '127.0.0.1',
    "user" => 'root',
    "password" => '/',
    "database" => 'doingsdone'
];

/**
 * Функция подключения к ДБ
 *
 * @param array $config_db Конфиг ДБ
 *
 * @return mysqli
 */
function connect_db($config_db)
{
$connection_db = mysqli_connect($config_db["host"], $config_db["user"], $config_db["password"], $config_db["database"]);

// проверка на ошибку подключения
if (!$connection_db) {
    print("Ошибка подключения к БД " . mysqli_connection_error());
    die();
}

// Задаём кодировку
mysqli_set_charset($connection_db, "utf8");
return $connection_db;
}
