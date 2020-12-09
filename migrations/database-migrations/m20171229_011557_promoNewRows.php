<?php

class m20171229_011557_promoNewRows extends Migrations
{
    public function up()
    {

        if ($this->isConfirmed(true) == true) return false;

        $sql = "
            ALTER TABLE `promo` 
            ADD COLUMN `title2` VARCHAR(100) NULL AFTER `top`,
            ADD COLUMN `title3` VARCHAR(45) NULL AFTER `title2`,
            ADD COLUMN `dateFrom` INT NULL AFTER `title3`,
            ADD COLUMN `dateTo` INT NULL AFTER `dateFrom`,
            ADD COLUMN `sale` INT NULL AFTER `dateTo`;
            ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `promo` 
                DROP COLUMN `dateTo`,
                DROP COLUMN `dateFrom`,
                DROP COLUMN `title3`,
                DROP COLUMN `sale`,
                DROP COLUMN `title2`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Экспорт страниц в вебпарсере";
    }

    public function isConfirmed($returnBoolean = false)
    {
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('promo');
        $result = isset($table->columns['dateTo']);

        if ($returnBoolean) {
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