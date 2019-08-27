<h2 class="content__main-heading">Добавление задачи</h2>

<form class="form"  action="add.php" method="post" autocomplete="off">
    <div class="form__row">
        <label class="form__label" for="name">Название <sup>*</sup></label>
        <?php $error = isset($errors["name"]) ? "form__input--error" : "";?>
        <input class="form__input <?=$error;?>" type="text" name="name" id="name" value="<?=get_post_val("name");?>" placeholder="Введите название">
    </div>

    <div class="form__row">
        <label class="form__label" for="project">Проект <sup>*</sup></label>
        <select class="form__input form__input--select" name="project" id="project">
            <?php foreach($all_projects as $project): ?>
            <option value=""><?=$project["project"]?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form__row">
        <label class="form__label" for="date">Дата выполнения</label>
        <input class="form__input form__input--date" type="text" name="date" id="date" value="<?=get_post_val("date");?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
    </div>

    <div class="form__row">
        <label class="form__label" for="file">Файл</label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="file" id="file" value="">
            <label class="button button--transparent" for="file">
                <span>Выберите файл</span>
            </label>
        </div>
    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="submit" value="Добавить">
    </div>
</form>