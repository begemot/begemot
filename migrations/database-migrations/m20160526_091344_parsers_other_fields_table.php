<?php

class m20160526_091344_parsers_other_fields_table extends Migrations
{
    public function up()
    {

        if ($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `parsers_other_fields` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NULL,
  `parsers_stock_id` INT NULL,
  `value` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`));
  ALTER TABLE `parsers_other_fields`
ADD COLUMN `filename` VARCHAR(45) NULL ;
ALTER TABLE `parsers_other_fields`
CHANGE COLUMN `parsers_stock_id` `parsers_stock_id` VARCHAR(200) NULL DEFAULT NULL ;

";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `parsers_other_fields`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Создание таблицы дополнительных данных парсера.";
    }

    public function isConfirmed($returnBoolean = false)
    {
        Yii::app()->db->schema->refresh();
        $result = Yii::app()->db->schema->getTable('parsers_other_fields');

        if ($returnBoolean)
            return $result;

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