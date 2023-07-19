<?php

class m20160520_084532_parser_stock_modifs_add extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `parsers_stock`
	ADD COLUMN `modifs` TEXT;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `parsers_stock`
	DROP COLUMN `modifs`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Поле с модификациями в парсере.";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('parsers_stock');
        $result = isset($table->columns['modifs']);

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