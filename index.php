<?php
// Входные данные
require_once "./init.php";
// Боковой блок с проектами пользователя
require_once "./aside_projects.php";

if (!empty($_SESSION)) {
    if (isset($_GET["search"]) && !empty($_GET["search"])) {
        // Подготавливаем хапрос на поиск
        $search = mysqli_real_escape_string($connection_db, trim($_GET["search"]));
        $sql = "SELECT name AS task_name, deadline AS complete_date, complete_status AS is_completed, project_id AS category, user_file AS file
                FROM task WHERE MATCH(name) AGAINST('$search') AND user_id = '{$_SESSION["id"]}'";
        // Условие на завершённость задачи
        if ($show_complete_tasks == 0) {
            $sql .= " AND complete_status = 0";
        }
        // Выполняем поиск и записываем результат
        $stmt = mysqli_query($connection_db, $sql);
        $tasks = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
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
