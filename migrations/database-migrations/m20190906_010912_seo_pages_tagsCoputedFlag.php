<?php

class m20190906_010912_seo_pages_tagsCoputedFlag extends Migrations
{
    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `seo_pages` 
                DROP COLUMN `tagsCoputedFlag`;";
        $this->execute($sql);

        return true;
    }

    public function up()
    {
        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `seo_pages` 
          ADD COLUMN `tagsCoputedFlag` INT DEFAULT 0 AFTER `status`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "такие дела";
    }

    public function isConfirmed($returnBoolean = false)
    {
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('seo_pages');
        $result = isset($table->columns['tagsCoputedFlag']);

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