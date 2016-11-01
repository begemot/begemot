<?php

class m20160829_011134_baseOptions extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catItemsToItems`
                ADD COLUMN `isBase` INT NULL DEFAULT 0 AFTER `toItemId`;
                ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catItemsToItems`
                    DROP COLUMN `isBase`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Базовые опции";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catItemsToItems');
        $result = isset($table->columns['isBase']);

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