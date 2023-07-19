<?php

class m20160922_122947_optionsOrder extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catItemsToItems`
	ADD COLUMN `order` INT NULL ;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catItemsToItems`
	DROP COLUMN `order`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Поле учета порядка опций";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catItemsToItems');
        $result = isset($table->columns['order']);

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