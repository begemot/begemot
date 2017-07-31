<?php

class m20160501_064142_modification_column extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catItems`
	ADD COLUMN `modOfThis` INT NULL;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catItems`
	DROP COLUMN `modOfThis`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Добавление поля modOfThis для определения модификации в catalog.";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catItems');
        $result = isset($table->columns['modOfThis']);

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