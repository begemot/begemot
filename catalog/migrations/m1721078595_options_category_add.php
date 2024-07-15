<?php

class m1721078595_options_category_add extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true)) return false;

        // Добавить новую запись в существующую таблицу `catCategory`
        $sql = "INSERT INTO `catCategory` 
                (`pid`, `name`, `text`, `order`, `dateCreate`, `dateUpdate`, `status`, `name_t`, `level`, `seo_title`, `layout`, `viewFile`, `itemViewFile`, `published`, `type`)
                VALUES 
                (-1, 'options', 'Описание раздела options', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 1, 'Перевод options', 0, '', '', '', '', 1, NULL);";
        Yii::app()->db->createCommand($sql)->execute();

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        // Удалить добавленную запись из таблицы `catCategory`
        $sql = "DELETE FROM `catCategory` WHERE `name` = 'options';";
        Yii::app()->db->createCommand($sql)->execute();

        return true;
    }

    public function getDescription()
    {
        return "Добавление нового раздела 'options' в таблицу catCategory";
    }

    public function isConfirmed($returnBoolean = false)
    {
        // Проверить, существует ли новая запись в таблице `catCategory`
        $sql = "SELECT COUNT(*) as count FROM `catCategory` WHERE `name` = 'options';";
        $count = Yii::app()->db->createCommand($sql)->queryScalar();
        
        return $count > 0;
    }
}
