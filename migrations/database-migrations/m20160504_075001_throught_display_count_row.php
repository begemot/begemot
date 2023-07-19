<?php

class m20160504_075001_throught_display_count_row extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catItemsToCat`
                ADD COLUMN `through_display_count` INT;

                CHANGE COLUMN `through_display_count` `through_display_count` INT(11) NULL DEFAULT 0 ;

                ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `boats`.`catItemsToCat`
                DROP COLUMN `through_display_count`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "такие дела";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catItemsToCat');
        $result = isset($table->columns['through_display_count']);

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