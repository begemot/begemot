<?php

class m20160518_094343_fieldModifId extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `webParserData`
            ADD COLUMN `fieldModifId` VARCHAR(500) NULL;
            ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `webParserData`
	    DROP COLUMN `fieldModifId`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Поле fieldModifId для парсера, что бы была возможность помечать в группы модификации товара.";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('webParserData');
        $result = isset($table->columns['fieldModifId']);

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