<?php
// Вспомогательные функции
require_once "./helpers.php";
// Собственные Функции
require_once "./functions.php";
// Подключение к БД
require_once "data_base/connection_db.php";

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

// Подключение к ДБ
$connection_db = connect_db($data_base);
// Получаем список проектов для пользователя по id
$projects = get_user_projects($user_id, $connection_db);
// Получаем список задач
$tasks = get_all_tasks($connection_db);

// Получаем списко задач для конкретного проекта по URI
if (isset($_GET["project_id"])){
    // Получаем id проекта
    $project_id = (int)($_GET["project_id"]);
    // Получаем проект по user_ id $$ project_id
    $project = get_user_projects_by_id($user_id, $project_id, $connection_db);

    // Если проетка по id для юзера не найдено то 404
    if(!$project){
        http_response_code(404);
        include("404.php");
        die();
    }

    // Получаем список задач для конкретного проекта
    $tasks = get_user_tasks_by_project_id($user_id, $project_id, $connection_db);
}

// Шаблон списка задач
$page = include_template("index.php", [
    "tasks" => $tasks,
    "show_complete_tasks" => $show_complete_tasks
]);

// Лейаут главной страницы
$page_layout = include_template("layout.php", [
    "projects" => $projects,
    "active_project" => (int)$project["id"],
    "tasks" => $tasks,
    "page" => $page,
    "user" => "Quokka",
    "title" => "Дела в порядке"
]);

//собирает вёрстку
print($page_layout);
