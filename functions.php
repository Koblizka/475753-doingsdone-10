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
    // Счётчик
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
 * если скоро дедлайн(менее или сутки)
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
    $sql = "SELECT name AS project, id FROM project
            ORDER BY id ASC";

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
    $sql = "SELECT name AS task_name, deadline AS complete_date, complete_status AS is_completed, project_id AS category, user_file AS file
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
    $sql = "SELECT t.name AS task_name, t.deadline AS complete_date, t.complete_status AS is_completed, t.project_id AS category, t.user_file AS file
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

/**
 * Валидирует поле имени/названия задания на пустую строку или превышение длины
 *
 * @param  string $name Название задания
 *
 * @return string|null Если не прошло валидацию или всё хорошо
 */
function validate_name(string $name)
{
    // Название задания
    $task_name = $_POST[$name];
    
    if (empty($task_name)) {
        return "Это поле должно быть заполнено";
    }

    // Отсебя добавил ограничение длины названия задачи
    if (strlen($task_name) >= 90) {
        return "Слишком длинное имя задачи";
    }

    return null;
}

/**
 * Валидирует поле выбора проекта. Есть ли такой созданный проект
 *
 * @param  string $project Выбранный проект пользователя 
 * @param  array $projects Список всех проектов
 *
 * @return string|null 
 */
function validate_project(string $project, array $projects)
{
    // Список разрешённых проектов
    $allowed_list = array_column($projects, "id");

    // Проверяем есть ли выбранный проект пользователя проект в разрешённом списке
    if (!in_array($project, $allowed_list)){
        return "Был выбран не существующий проект";
    }

    return null;
}

/**
 * Валидация даты на корректность формата и самой даты
 *
 * @param  string $date дата
 *
 * @return string|null Сообщение об ошибке или всё хорошо - null
 */
function validate_date(string $date)
{
    // Текущая дата
    $current_date = time() - SEC_IN_A_DAY;
    // Проверяем валидность формата даты
    if (!is_date_valid($date)){
        return "Указан не верный формат даты. Должен быть ГГГГ-ММ-ДД";
    }

    // Проверяем валидность указаной даты
    if (strtotime($date) <= $current_date){
        return "Указана не верная дата. Дата должна быть больше или равно текущей";
    }

    return null;
}
