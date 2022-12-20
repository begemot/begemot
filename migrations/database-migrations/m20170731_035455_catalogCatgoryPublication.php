<?php

class m20170731_035455_catalogCatgoryPublication extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catCategory`
ADD COLUMN `published` INT NULL DEFAULT 1;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catCategory`
	DROP COLUMN `published`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Доп. поле для определения публикации на сайте раздела каталога.";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catCategory');
        $result = isset($table->columns['published']);

        if($returnBoolean){
            return $result;
        }

        return parent::confirmByWords($result);
    }

    /*
     * ALTER TABLE `catItems`
    DROP COLUMN `top`;
     *
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}