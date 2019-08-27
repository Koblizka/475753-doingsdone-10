<?php
/**
 * Считает количтесвто заданий в категории
 *
 * @param  array $task Задания
 * @param  array $project_name Категории
 *
 * @return int $num Количество заданий в категории
 */
function task_count($tasks, $project_name)
{
    $num = 0;

    foreach ($task as $task_data) {
        if ($task_data["category"] === $project_name) {
            $num++;
        }
    }

    return $num;
}

/**
 * функция принимает строку содержащую дату заврешение таска
 * если скоро дедлайн(менее или сутки), то добавляет css класс важности задачи
 *
 * @param  str $task Строка содержащая дату
 *
 * @return  bool Возвращает true | false
 */
function important_task($task)
{
    if ($task === NULL) {
        return false;
    } elseif ($task !== NULL && strtotime($task) - time() <= SEC_IN_A_DAY) {
        return true;
    }

    return "";
}

/**
 * Возвращает дату или "Нет" при отсутствии даты или неверном значении
 *
 * @param  str $task Дата
 *
 * @return str Дата|null отсутствии даты или неверном значении
 */
function get_date($task)
{
    return $task ? date("d.m.Y", strtotime($task)) : null;
}

/**
 * Получаем список всех проектов
 *
 * @param  mysqli $connection_db Подключение к БД
 *
 * @return arr Ассоциативный массив с проектами или пустой массив
 */
function get_all_projects(mysqli $connection_db)
{
    // Формируем запрос на список всех проектов сортированный по id 
    $sql = "SELECT name AS project, id AS project_id FROM project
            ORDER BY project_id ASC";

    // Проверка на корректность запроса
    $result = mysqli_query($connection_db, $sql) ?: [];
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Возвращает список проектов из базы данных в виде ассоциативного массива
 *
 * @param  int $user_id
 * @param  mysqli $connection_db Подключение к БД
 *
 * @return arr Ассоциативный массив с проектами или пустой массив
 */
function get_user_projects(int $user_id, mysqli $connection_db)
{
    // Формируем запрос на список проектов
    $sql = "SELECT p.name AS project, p.id, COUNT(t.id) task_count
    FROM project p
    LEFT JOIN task t
    ON t.project_id = p.id WHERE p.user_id = $user_id
    GROUP BY p.name, p.id ORDER BY task_count DESC";

    // Запрос на список проектов
    // Проверка на корректность запроса
    $result = mysqli_query($connection_db, $sql) ?: [];

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Возвращает ассоциативный массив с параметрами конкретного проекта по id
 *
 * @param  int $user_id Пользователя id
 * @param  int $project_id Проекта id
 * @param  mysqli $connection_db Подключение к ДБ
 *
 * @return arr Ассоциативный массив - Проект
 */
function get_user_projects_by_id(int $user_id, int $project_id, mysqli $connection_db)
{
    // Формируем запрос на список проектов
    $sql = "SELECT p.name AS project, p.id, COUNT(t.id) task_count
    FROM project p
    LEFT JOIN task t
    ON t.project_id = p.id WHERE p.user_id = $user_id && p.id = $project_id
    GROUP BY p.name, p.id ORDER BY task_count DESC";

    // Запрос на список проектов
    // Проверка на корректность запроса
    $result = mysqli_query($connection_db, $sql) ?: [];


    return mysqli_fetch_assoc($result);
}

/**
 * Выводит все существующие задания
 *
 * @param  mysqli $connection_db Подключаемс к ДБ
 *
 * @return arr Список задач
 */
function get_all_tasks(mysqli $connection_db)
{
    // Получаем списк задач
    $sql = "SELECT name AS task_name, deadline AS complete_date, complete_status AS is_completed, project_id AS category
            FROM task";

    // Проверка на корректность запроса
    $result = mysqli_query($connection_db, $sql) ?: [];

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выводит спсок задач по id проекта
 *
 * @param  int $project_id Проекта id
 * @param  mysqli $connection_db Подключаемся к ДБ
 *
 * @return arr Список задач по id проекта
 */
function get_user_tasks_by_project_id(int $user_id, int $project_id, mysqli $connection_db)
{
    // Получаем списк задач
    $sql = "SELECT t.name AS task_name, t.deadline AS complete_date, t.complete_status AS is_completed, t.project_id AS category
            FROM task t
            JOIN user u
            ON u.id = t.user_id WHERE t.user_id = $user_id && t.project_id = $project_id";

    // Проверка на корректность запроса
    $result = mysqli_query($connection_db, $sql) ?: [];


    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Сохраняет значения подставленные пользователем в форме
 *
 * @param  str $name Введённое значение пользователя в форме
 *
 * @return str Значение поля из формы или пустая строка если такого нет
 */
function get_post_val(string $name)
{
    return $_POST[$name] ?? "";
}

function validate_name($name)
{
    $task_name = $_POST[$name];
    
    if (empty($task_name)) {
        return "Это поле должно быть заполнено";
    }

    return null;
}

function validate_project(string $project, array $projects)
{
    $project_name = $_POST[$project];
    $allowed_list = array_column($projects, "name");

    if (!in_array($project, $allowed_list)){
        return "Выберан не существующий проект";
    }

    return null;
}
