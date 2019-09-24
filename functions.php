<?php

/**
 * Возвращает ответ на запрос в виде массива 
 *
 * @param  string $sql Запрос
 * @param  mysqli $connection_db Подключение к ДБ
 *
 * @return array Ответ на запрос
 */
function get_query_result(string $sql, mysqli $connection_db) 
{
    // $sql = mysqli_real_escape_string($connection_db, $sql);
    $result = mysqli_query($connection_db, $sql);

    return mysqli_fetch_assoc($result);
}

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
    ON t.project_id = p.id WHERE p.user_id = '$user_id'
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
    ON t.project_id = p.id WHERE p.user_id = '$user_id' && p.id = '$project_id'
    GROUP BY p.name, p.id ORDER BY task_count DESC";

    // Запрос на список проектов
    // Проверка на корректность запроса
    $result = mysqli_query($connection_db, $sql) ?: [];


    return mysqli_fetch_assoc($result);
}

/**
 * Выводит все существующие задания пользователя. 
 *
 * При указании $project_id задания только этого проекта. 
 * При указании $filter добавляется фильтр по датам
 *
 * @param  mysqli $connection_db Подключаемс к ДБ
 * @param int $user_id
 * @param int $project_id
 * @param string $filter
 *
 * @return arr Список задач
 */
function get_all_user_tasks(mysqli $connection_db, int $user_id, int $project_id = null, string $filter = null)
{
    // Получаем списк задач
    $sql = "SELECT name AS task_name, id AS task_id, deadline AS complete_date, complete_status AS is_completed, project_id AS category, user_file AS file
            FROM task WHERE user_id = '$user_id'";

    // Если задан id проекта, то добавляем в запрос
    if (isset($project_id)) {
        $sql .= " AND project_id = '$project_id'";
    }

    // Если задан фильтр, то добавляем в запрос
    if (isset($filter)) {

        switch ($filter) {
            case "today":
                $sql .= " AND deadline = CURDATE()";
                break;
            case "tomorrow":
                $sql .= " AND DATE_ADD(CURDATE(), INTERVAL 1 DAY) = deadline";
                break;
            case "expired":
                $sql .= " AND CURDATE() > deadline";
                break;
            default:
                $sql .= "";
        }
    }

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
        return "Слишком длинное имя";
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

/**
 * Валидация Имейла пользователяю Должен быть уникален
 *
 * @param  string $email
 * @param  mysqli $connection_db
 *
 * @return string|null Текст ошибки или null если всё ок
 */
function validate_email(string $email, mysqli $connection_db = null)
{
    if (empty($email)) {
        return "Введите имейл";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Имейл указан не верно";
    } elseif (isset($connection_db)) { // Условия для аутентификации
        $email = mysqli_real_escape_string($connection_db, $email);

        $sql = "SELECT id FROM user WHERE email = '$email'";
        $result = mysqli_query($connection_db, $sql);
    
        if (mysqli_num_rows($result) > 0) {
            return "Такой пользователь уже зарегистрирован";
        }
    }

    return null;
}

/**
 * Валидируем Логин пользователя. Он не должен быть больше 20 исмволов и должен быть уникален
 *
 * @param  string $login
 * @param  mysqli $connection_db
 *
 * @return string|null Текст ошибки или всё ок null
 */
function validate_login(string $login, mysqli $connection_db = null)
{

    if (empty($login)) {
        return "Это поле должно быть заполнено";
    } elseif (strlen($login) >= 20) {
        return "Слишком длинное имя";
    } elseif ($connection_db) {
        $login = mysqli_real_escape_string($connection_db, $login);

        $sql = "SELECT id FROM user WHERE name = '$login'";
        $result = mysqli_query($connection_db, $sql);

        if (mysqli_num_rows($result) > 0) {
            return "Такой пользователь уже зарегистрирован";
        }
    }

    return null;
}

/**
 * Валидация пароля. Также при указании необязетльных аргументов работает как аутентификатор. Не менее 6 символов
 *
 * @param  string $password
 * @param  string $emal Треубется для аутенитицикации
 * @param  mysqli $connection_db Треубется для аутенитицикации
 *
 * @return string|null Текст ошибки или всё ок null
 */
function validate_password(string $password, string $email = null, mysqli $connection_db = null)
{
    if (empty($password)) {
        return "Необходимо ввести пароль";
    } elseif (strlen($password) < 6) {
        return "Слишком короткий пароль";
    } elseif (isset($connection_db) && isset($email)) {
        $email = $email;

        $sql = "SELECT password FROM user WHERE email = '$email'";
        $result = mysqli_query($connection_db, $sql);
        $pswd = mysqli_fetch_assoc($result);

        if (!password_verify($password, $pswd["password"])) {
            return "Пароль или имейл введён не верно";
        }
    }

    return null;
}