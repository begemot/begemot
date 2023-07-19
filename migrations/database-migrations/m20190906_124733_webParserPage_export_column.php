<?php

class m20190906_124733_webParserPage_export_column extends Migrations
{
    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `webParserPage` 
                DROP COLUMN `export`;";
        $this->execute($sql);

        return true;
    }

    public function up()
    {
        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `webParserPage` 
          ADD COLUMN `export` INT DEFAULT 0 AFTER `mime`;";
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
        $table = Yii::app()->db->schema->getTable('webParserPage');
        $result = isset($table->columns['export']);

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