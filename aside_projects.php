<?php
// Входные данные
require_once "./init.php";

if (isset($_SESSION["id"])) {

    // Получаем список проектов для пользователя по id
    $projects = get_user_projects($_SESSION["id"], $connection_db);
    
    // Получаем списко задач для конкретного проекта по URI
    if (isset($_GET["project_id"])){
        // Получаем id проекта
        $project_id = (int)($_GET["project_id"]);
        // Получаем проект по user_ id $$ project_id
        $project = get_user_projects_by_id($_SESSION["id"], $project_id, $connection_db);
    
        // Если проетка по id для юзера не найдено то 404
        if(!$project){
            http_response_code(404);
            include("404.php");
            die();
        }
    
        // Получаем список задач для конкретного проекта
        $tasks = get_user_tasks_by_project_id($_SESSION["id"], $project_id, $connection_db);
    } else {
        $tasks = get_all_user_tasks($_SESSION["id"], $connection_db); // Получаем список всех задач
    }
}


