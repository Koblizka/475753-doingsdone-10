<?php
// Входные данные
require_once "./init.php";
// Вспомогательные функции
require_once "./aside_projects.php";

// Нужно будет прикрутить здесь аутентификацию пользователя в следующем задании

// Получаем список всех проектов для селекта в форме
$all_projects = get_all_projects($connection_db);
// Массив для хранения ошибок валидации
$errors = [];

// Проверка отправки формы
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Массив всех полей отправленной формы
    $task = $_POST;
    // Поля формы для валидации
    $required = ["name", "project"];
    // Мссив с функциями для валидации
    $rules = [
        "name" => function()
        {
            return validate_name("name");
        },
        "project" => function() use ($all_projects)
        {
            return validate_project($_POST["project"], $all_projects);
        },
        "date" => function()
        {
            return validate_date($_POST["date"]);
        }
    ];

    // Применяем функции для обработки полей формы
    foreach ($required as $field) {
        if (isset($_POST[$field])){
            $rule = $rules[$field];
            $errors[$field] = $rule();
        }
    }

    // Валидация поля дата   
    if (!empty($_POST["date"])){
        $errors["date"] = $rules["date"]();
    } else {
        $task["date"] = null;
    }

    // Фильтруем ошибки от null. Кажется логичным вынести за следующий блок if, но тогда errors со значением null далее мешает
    $errors = array_filter($errors);

    // Был ли загружен фаил и загружен ли он через POST
    if (isset($_FILES["file"]) && is_uploaded_file($_FILES["file"]["tmp_name"])){
        // Если есть ошибки в форме, то не отправим фаил. Иначе уведомим
        if (!empty($errors)) {
            $errors["file"] = var_dump($errors);
        } else {
            // Временное имя файла
            $tmp_name = $_FILES["file"]["tmp_name"];
            // Реальное имя файла
            $file_name = $_FILES["file"]["name"]; 
            // Уникальное имя для загруженного файла
            $file_uname = uniqid() . $file_name;
            // Путь к директории с файлом
            $path = $_SERVER["DOCUMENT_ROOT"] . "/uploads/";
            // УРЛ файла для ссылок
            $task["file"] = $file_uname;

            // Перемещаем файл в директорию для файлов
            move_uploaded_file($tmp_name, $path . $file_uname);
        }
    } else {
        $task["file"] = "";
    }

    // Если есть ошибки, то выводим их в шаблон иначе сохраняем задачу в БД
    if (count($errors)) {
        // Шаблон для создания здачи
        $page = include_template("form.php", [
            "all_projects" => $all_projects,
            "errors" => $errors
        ]);
    } else {
        // Создаём запрос для добавления задачи в БД
        $sql = "INSERT INTO task ( name, date_creation, deadline, user_id, project_id, complete_status, user_file ) VALUE (? , CURDATE(), ?, ?, ?, 0, ?)";
        $stmt = db_get_prepare_stmt($connection_db, $sql, [$task["name"], $task["date"], $user_id, $task["project"], $task["file"]]);
        $result = mysqli_stmt_execute($stmt);

        if  ($result){
            header("Location: /");
            exit();
        } else {
            print_r(mysqli_error($connection_db));
        }
    }
}


// Шаблон для создания здачи
$page = include_template("form.php", [
    "all_projects" => $all_projects,
    "errors" => $errors
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
