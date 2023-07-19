<?php

class m20160609_061050_webparser_data_fields extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `webParserData`
ADD COLUMN `sourcePageUrl` MEDIUMTEXT NULL,
ADD COLUMN `fieldParentId` VARCHAR(500) NULL ,
ADD COLUMN `fieldGroupId` VARCHAR(500) NULL ;
";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `webParserData`
                DROP COLUMN `fieldGroupId`,
                DROP COLUMN `fieldParentId`,
                DROP COLUMN `sourcePageUrl`;
                ";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Необходимые полня для webParser";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('webParserData');
        $result = isset($table->columns['fieldGroupId']);

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