<h2 class="content__main-heading">Добавление задачи</h2>

<form class="form"  action="add.php" method="post" autocomplete="off" enctype="multipart/form-data">
    <div class="form__row">
        <label class="form__label" for="name">Название <sup>*</sup></label>
        <?php $error = isset($errors["name"]) ? "form__input--error" : "";?>
        <input class="form__input <?=$error;?>" type="text" name="name" id="name" value="<?=get_post_val("name");?>" placeholder="Введите название">
        <div class="form__message"><?=$errors["name"] ?? "";?></div>
    </div>

    <div class="form__row">
        <label class="form__label" for="project">Проект <sup>*</sup></label>
        <?php $error = isset($errors["project"]) ? "form__input--error" : "";?>
        <select class="form__input form__input--select <?=$error;?>" name="project" id="project" value="<?=get_post_val("project");?>">
            <?php foreach($all_projects as $project): ?>
                <option value="<?=$project["id"];?>" <?=get_post_val("project") === $project["id"] ? "selected" : "";?>><?=htmlspecialchars($project["project"])?></option>
            <?php endforeach; ?>
        </select>
        <div class="form__message"><?=$errors["project"] ?? "";?></div>
    </div>

    <div class="form__row">
        <label class="form__label" for="date">Дата выполнения</label>
        <?php $error = isset($errors["date"]) ? "form__input--error" : "";?>
        <input class="form__input form__input--date <?=$error?>" type="text" name="date" id="date" value="<?=get_post_val("date");?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
        <div class="form__message"><?=$errors["date"] ?? "";?></div>
    </div>

    <div class="form__row">
        <label class="form__label" for="file">Файл</label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="file" id="file" value="">
            <label class="button button--transparent" for="file">
                <span>Выберите файл</span>
            </label>
            <div class="form__message"><?=$errors["file"] ?? "";?></div>
        </div>
    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="submit" value="Добавить">
    </div>
</form>