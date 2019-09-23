<?php
// Входные данные
require_once "./init.php";
// Боковой блок с проектами пользователя
require_once "./aside_projects.php";

if (!isset($_SESSION["id"])) {
    header("Location: /");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["name"])) {
        $errors["name"] = "Необходимо указать имя проекта";
    } else {
        // Проверка на существование проекта в БД
        $project = mysqli_real_escape_string($connection_db, $_POST["name"]);
        $sql = "SELECT * FROM project WHERE name = '$project' AND user_id = '{$_SESSION["id"]}'";
        $result = mysqli_query($connection_db, $sql);

        if (mysqli_num_rows($result) > 0) {
            $errors["name"] = "Такой проект уже существует";
        } else {
            // Если проекта такого не существует, то создаём его
            $sql = "INSERT INTO project (name, user_id) VALUES (?, ?)";
            $stmt = db_get_prepare_stmt($connection_db, $sql, [$_POST["name"], $_SESSION["id"]]);
            $result = mysqli_stmt_execute($stmt);
            // Если всё ок, то переадресовываем пользователя на главную
            if ($result) {
                header("Location: /");
                exit();
            }
        }
    }
}

$page = include_template("add_project.php", [
    "errors" => $errors
]);

$page_layout = include_template("layout.php", [
    "projects" => $projects,
    "active_project" => (int)$project["id"], 
    // "tasks" => $tasks,
    "page" => $page,
    "user" => $_SESSION["name"],
    "title" => "Дела в порядке"
]);

print($page_layout);