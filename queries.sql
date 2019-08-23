USE `doingsdone`;

INSERT INTO `project` ( name, user_id )
VALUES
      ('Входящие', 1),
      ('Учёба', 2),
      ('Работа', 1),
      ('Домашние дела', 2),
      ('Авто', null);


INSERT INTO `user` ( name, email, password, date_registration )
VALUES
      ('vasja', 'mail@mail.ru', 'qwery', '2018-01-01' ),
      ('petja', 'mail1@mail.ru', 'asdf', '2018-01-01' );


INSERT INTO `task` ( name, date_creation, deadline, user_id, project_id, complete_status )
VALUES
      ('Собеседование в IT компании', '2019-04-22', '2019-04-22', 1, 3, 0),
      ('Выполнить тестовое задание', '2018-04-22', '2018-12-25', 1, 3, 0),
      ('Сделать задание первого раздела', '2018-04-22', '2018-12-21', 2, 2, 0),
      ('Встреча с другом', '2018-04-22', '2018-12-22', 1, 1, 0),
      ('Купить корм для кота', '2018-04-22', null, 2, 4, 0),
      ('Заказать пиццу', '2018-04-22', null, 2, 4, 0);

-- получить список из всех проектов для одного пользователя. Объедините проекты с задачами, чтобы посчитать количество задач в каждом проекте и в дальнейшем выводить эту цифру рядом с именем проекта;
SELECT u.name,p.name, COUNT(t.id) task_count FROM project p
JOIN user u
ON u.id = p.user_id
LEFT JOIN task t
ON t.project_id = p.id WHERE p.user_id = 1
GROUP BY p.name;

-- получить список из всех задач для одного проекта;
SELECT t.name FROM task t
JOIN project p
ON p.id = t.project_id WHERE p.id = 4;

-- пометить задачу как выполненную;
UPDATE task SET complete_status = 1 WHERE id = 3;

-- обновить название задачи по её идентификатору.
UPDATE task SET name = 'Покурить бамбук' WHERE id = 3;
