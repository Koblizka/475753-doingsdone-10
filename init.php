<?php
session_start();

// Вспомогательные функции
require_once "./helpers.php";
// Собственные Функции
require_once "./functions.php";
// Конфиг БД
require_once "./data_base/connection_db.php";

// показывать или нет выполненные задачи
$show_complete_tasks = 0;
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
// Массив для хранения ошибок валидации
$errors = [];
// Фильтр по датам задач
$filter = null;

