<?php

class m1741024004_add_modification_cat extends BaseMigration
{
    private $modificationData = [
        'pid' => -1,
        'name' => 'modification',
        'text' => 'Category for modifications',
        'order' => 0,
        'dateCreate' => null,
        'dateUpdate' => null,
        'status' => 1,
        'name_t' => 'modification',
        'level' => 0,
        'seo_title' => 'Modifications',
        'layout' => '',
        'viewFile' => '',
        'itemViewFile' => '',
        'published' => 1,
        'type' => null
    ];

    public function up()
    {
        if ($this->isConfirmed(true) == true) return false;

        $connection = Yii::app()->db;
        $command = $connection->createCommand();

        $command->insert('catCategory', $this->modificationData);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "DELETE FROM `catCategory` WHERE `name` = 'modification' AND `pid` = -1";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Добавление раздела Modification в таблицу catCategory";
    }

    public function isConfirmed($returnBoolean = false)
    {
        $connection = Yii::app()->db;
        $sql = "SELECT COUNT(*) FROM `catCategory` WHERE `name` = 'modification' AND `pid` = -1";
        $count = $connection->createCommand($sql)->queryScalar();

        return $count > 0;
    }
}