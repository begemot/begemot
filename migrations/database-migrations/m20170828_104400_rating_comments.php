<?php

class m20170828_104400_rating_comments extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `comments` DROP `likes`, DROP `dislikes`;
        ALTER TABLE `comments` ADD `is_admin` INT(1) NOT NULL DEFAULT '0' AFTER `status`, ADD `rating` INT NOT NULL DEFAULT '0' AFTER `is_admin`;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `comments` ADD `likes` INT(11) NOT NULL DEFAULT '0', ADD `dislikes`  INT(11) NOT NULL DEFAULT '0', DROP `rating`, DROP `is_admin`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Добавление поля ration и is_admin в таблицу comments.";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('comments');
        $result = isset($table->columns['is_admin']) && isset($table->columns['rating']);

        if($returnBoolean){
            return $result;
        }

        return parent::confirmByWords($result);
    }

}