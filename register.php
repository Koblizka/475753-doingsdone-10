<?php
require_once "./init.php";
// require_once "./aside_projects.php";

if ($_SERVER["REQUEST__METHOD"] === "POST"){

}

// Шаблон для создания здачи
$page = include_template("register.php", [
    // "all_projects" => $all_projects
]);

// Лейаут страницы формы
// $page_layout = include_template("layout.php", [
//     // "projects" => $projects,
//     // "active_project" => (int)$project["id"],
//     "page" => $page,
//     "user" => "Quokka",
//     "title" => "Дела в порядке"
// ]);

// Собирает вёрстку
print($page);
