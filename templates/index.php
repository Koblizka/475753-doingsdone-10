<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="./index.php" method="get" autocomplete="off">
    <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item <?=$filter === null ? "tasks-switch__item--active" : "";?>">Все задачи</a>
        <a href="/?filter=today" class="tasks-switch__item <?=$filter === "today" ? "tasks-switch__item--active" : "";?>">Повестка дня</a>
        <a href="/?filter=tomorrow" class="tasks-switch__item <?=$filter === "tomorrow" ? "tasks-switch__item--active" : "";?>">Завтра</a>
        <a href="/?filter=expired" class="tasks-switch__item <?=$filter === "expired" ? "tasks-switch__item--active" : "";?>">Просроченные</a>
    </nav>

    <label class="checkbox">
        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= $show_complete_tasks ? "checked" : "" ?> >
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php if (!empty($tasks)): ?>
    <?php foreach($tasks as $task_data): ?>
    <?php if($show_complete_tasks || $task_data["is_completed"] === "0"):?>
    <tr class="tasks__item task
    <?= $task_data["is_completed"] === "1" ? "task--completed" : "" ?>
    <?=important_task($task_data["complete_date"]) ? "task--important" : "Нет" ?>">
        <td class="task__select">
            <label class="checkbox task__checkbox">
                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?=$task_data["task_id"]?>" <?= $task_data["is_completed"] === "1" ? "checked" : "" ?>>
                <span class="checkbox__text"><?=htmlspecialchars($task_data["task_name"])?></span>
            </label>
        </td>
        <td class="task__file">
        <?php if ($task_data["file"]): ?>
            <a class="download-link" href="<?="/uploads/" . $task_data["file"]?>"><?=$task_data["file"]?></a>
        <?php endif; ?>
        </td>
        <td class="task__date <?=important_task($task_data["complete_date"]) ? "task--important" : "Нет" ?>"><?=get_date($task_data["complete_date"])?></td>
    </tr>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php else: ?>
        <p>Ничего не найдено по вашему запросу</p>
    <?php endif; ?>
</table>
