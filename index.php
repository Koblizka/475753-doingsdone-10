<?php
// Входные данные
require_once "./init.php";
// Боковой блок с проектами пользователя
require_once "./aside_projects.php";

if (!empty($_SESSION)) {
    // Создаём куку, которая помнит статус показа задач
    if (isset($_GET["show_completed"])) {
        $show_completed = (int)$_GET["show_completed"];
        setcookie("show_completed", $show_completed, 0, '/');
    }

    // Показ выполненных задач с помощи куки
    if (isset($_COOKIE["show_completed"])) {
        $show_complete_tasks = (int)$_COOKIE["show_completed"];
    }

    // Полнотекстовый поиск
    if (isset($_GET["search"]) && !empty($_GET["search"])) {
        // Подготавливаем хапрос на поиск
        $search = mysqli_real_escape_string($connection_db, trim($_GET["search"]));
        $sql = "SELECT name AS task_name, id AS task_id, deadline AS complete_date, complete_status AS is_completed, project_id AS category, user_file AS file
                FROM task WHERE MATCH(name) AGAINST('$search') AND user_id = '{$_SESSION["id"]}'";
        // Условие на завершённость задачи
        if ($show_complete_tasks == 0) {
            $sql .= " AND complete_status = 0";
        }
        // Выполняем поиск и записываем результат
        $stmt = mysqli_query($connection_db, $sql);
        $tasks = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
    }

    // Выполнение задачи
    if (isset($_GET["check"])) {
        $task_id = (int)$_GET["task_id"];
        $check_status = (int)$_GET["check"];
        // Обновляем статус задачи
        $sql = "UPDATE task SET complete_status = '$check_status' WHERE id = '$task_id'";
        $result = mysqli_query($connection_db, $sql);
    }

    // Фильтрация по задачам
    if (isset($_GET["filter"])) {
        $filter = mysqli_real_escape_string($connection_db, $_GET["filter"]);
        $tasks = get_all_user_tasks($connection_db, $_SESSION["id"], null, $filter);
    }

    // Шаблон списка задач
    $page = include_template("index.php", [
        "tasks" => $tasks,
        "show_complete_tasks" => $show_complete_tasks,
        "filter" => $filter
    ]);

    // Лейаут главной страницы
    $page_layout = include_template("layout.php", [
        "projects" => $projects,
        "active_project" => (int)$project["id"],
        "tasks" => $tasks,
        "page" => $page,
        "user" => $_SESSION["name"],
        "title" => "Дела в порядке"
    ]);
} else {
    $page_layout = include_template("layout.php", [
        "page" => include_template("guest.php")
    ]);
}

// Cобирает вёрстку
print($page_layout);
