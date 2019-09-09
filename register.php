<?php
require_once "./init.php";
require_once "./functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    // Массив всех полей отправленной формы
    $form = $_POST;
    // Поля формы для валидации
    $required = ["name", "email", "password"];
    // Массив с функциями для валидации
    $rules = [
        "name" => function() use ($connection_db)
        {
            return validate_login($_POST["name"], $connection_db);
        },
        "email" => function() use ($connection_db)
        {
            return validate_email($_POST["email"], $connection_db);
        },
        "password" => function()
        {
            return validate_password($_POST["password"]);
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
        // Шаблон для ошибок регистрации
        $page = include_template("register.php", [
            "errors" => $errors
        ]);
    } else {
        // Хэшируем пароль
        $password = password_hash($form["password"], PASSWORD_DEFAULT);

        // Создаём запрос для добавления пользователя в БД
        $sql = "INSERT INTO user ( name, password, email, date_registration ) VALUE (?, ?, ?, CURDATE())";
        $stmt = db_get_prepare_stmt($connection_db, $sql, [$form["name"], $password, $form["email"]]);
        $result = mysqli_stmt_execute($stmt);
        // Если всё ок, то редиректим на главную
        if  ($result){
            header("Location: /");
            exit();
        } else {
            print_r(mysqli_error($connection_db));
        }
    }

}

// Деволтный шаблон регистрации пользователя
$page = include_template("register.php", [
    "errors" => $errors
]);


// Собирает вёрстку
print($page);
