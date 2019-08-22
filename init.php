<?php
// Вспомогательные функции
require_once "./helpers.php";
// Собственные Функции
require_once "./functions.php";
// Конфиг БД
require_once "./data_base/connection_db.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
// секунд в сутках
const SEC_IN_A_DAY = 86400;
// список проектов
$projects = [];
// задачи
$tasks = [];
// Ид проекта
$project_id = null;
// Проект пользователя по id
$project = null;
// Юзер id
$user_id = 1;
