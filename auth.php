<?php
// Входные данные
require_once "./init.php";
// Пользовательские функции
require_once "./functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    // Поля формы для валидации
    $required = ["email", "password"];
    // Массив с функциями для валидации
    $rules = [
        "email" => function()
        {
            return validate_email($_POST["email"]);
        },
        "password" => function() use ($connection_db)
        {
            return validate_password($_POST["password"], $_POST["email"], $connection_db);
        }
    ];
    // Применяем функции валидации к полям формы
    foreach ($required as $field) {
        if (isset($_POST[$field])){
            $rule = $rules[$field];
            $errors[$field] = $rule();
        }
    }
    // Фильтруем массив ошибок на пустые значения
    $errors = array_filter($errors);

    // Если есть ошибки, то выводим их в шаблон иначе сохраняем задачу в БД
    if (count($errors)) {
        // Шаблон для ошибок аутентификации
        $page = include_template("auth.php", [
            "errors" => $errors
        ]);
    } else {
        // Создаём запрос сессию для пользователя
        $sql = "SELECT * FROM user WHERE email = '{$_POST["email"]}'";
        $user = get_query_result($sql, $connection_db);

        // Записываем данные из запроса в сессию
        $_SESSION["name"] = $user["name"];
        $_SESSION["id"] = $user["id"];

        header("Location: /index.php");
        exit();
    } 
} elseif (!empty($_SESSION)) {
    header("Location: /index.php");
    exit();
    
}

// Дефолтный шаблон регистрации пользователя
$page = include_template("auth.php", [
    "errors" => $errors
]);


// Собирает вёрстку
print($page);