<?php
// Входные данные
require_once "./init.php";
// Вспомогательные функции
require_once "./aside_projects.php";

// Нужно будет прикрутить здесь аутентификацию пользователя в следующем задании

// Получаем список всех проектов для селекта в форме
$all_projects = get_all_projects($connection_db);

// $errors = [];

// Проверка отправки формы
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Массив всех полей отправленной формы
    $task_form = $_POST;
    // Поля формы для валидации
    $required = ["name", ''];
    // Массив для хранения ошибок валидации
    $errors = [];
    // Мссив с функциями для валидации
    $rules = [
        "name" => function() {return validate_name("name");}
    ];

    // Применяем функции для обработки полей формы
    foreach ($required as $field) {
        if (!empty($_POST[$field])){
            $rule = $rules[$field];
            $errors[$field] = $rule();
        }
    }

    // Фильтруем ошибки    
    $errors = array_filter($errors);

    if (isset($errors)) {
        // Шаблон для создания здачи
        $page = include_template("form.php", [
            "all_projects" => $all_projects,
            "errors" => $errors
        ]);
        }
}

// Проверка обязаетльности полей


// Шаблон для создания здачи
$page = include_template("form.php", [
    "all_projects" => $all_projects,
]);

// Лейаут страницы формы
$page_layout = include_template("layout.php", [
    "projects" => $projects,
    "active_project" => (int)$project["id"],

    "page" => $page,
    "user" => "Quokka",
    "title" => "Дела в порядке"
]);

// Собирает вёрстку
print($page_layout);
