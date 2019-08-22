<?php
// Входные данные
require_once "./init.php";
// Вспомогательные функции
require_once "./aside_projects.php";

// Получаем список всех проектов
$all_projects = get_all_projects($connection_db);

// Шаблон для создания здачи
$page1 = include_template("form.php", [
    "all_projects" => $all_projects
]);

// Лейаут страницы формы
$page_layout = include_template("layout.php", [
    "projects" => $projects,
    "active_project" => (int)$project["id"],
    "tasks" => $tasks,
    "page" => $page1,
    "user" => "Quokka",
    "title" => "Дела в порядке"
]);

// Собирает вёрстку
print($page_layout);
