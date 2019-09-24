<?php
// Входные данные
include_once "./init.php";
// Подтягиваем SwiftMailer
include_once './vendor/autoload.php';


// Создаём объект для отправки имейла
$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);

// Формируем запрос на всех пользователей с дедлайном таск на сегодня
$sql = "SELECT u.id, u.name, u.email FROM task t JOIN user u ON t.user_id = u.id WHERE complete_status = 0 AND deadline = CURDATE() GROUP BY u.id";
$result = mysqli_query($connection_db, $sql);
$users_with_curr_day_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Если пользователи с дедлайном есть, то шлём им письма
if (!empty($users_with_curr_day_tasks)) {
    // Перебираем всех пользователей
    foreach ($users_with_curr_day_tasks as $users => $user) {
        // Находим все таски с дедлайном у пользователя
        $sql = "SELECT name, deadline FROM task WHERE user_id = '{$user["id"]}' AND complete_status = 0 AND deadline = CURDATE()";
        $result = mysqli_query($connection_db, $sql);
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        // Подгтоваляиваем шаблон письма
        $msg = "Уважаемый, " . $user["name"] . ". У Вас запланированы задачи: ";
        // Дополняем-уточняем шаблон письма тасками 
        foreach ($tasks as $task) {
            $msg .= "<br>" . $task["name"]. " на " . $task["deadline"];
        }
        // Формируем письмо
        $message = new Swift_Message("Уведомление от сервиса «Дела в порядке»");
        $message->setFrom(["keks@phpdemo.ru" => "keks@phpdemo.ru"]);
        $message->setBcc($user["email"]);
        $message->setBody($msg, "text/html");
        // Отправляем письмо
        $result = $mailer->send($message);

        if ($result) {
            print("Рассылка успешно отправлена");
        }
        else {
            print("Не удалось отправить рассылку");
        }
    }
}
