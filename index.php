<?php
// Входные данные
require_once "./init.php";
// Боковой блок с проектами пользователя
require_once "./aside_projects.php";

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

// Cобирает вёрстку
print($page_layout);
