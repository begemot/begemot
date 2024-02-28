<?php

class m1697198264_addType extends BaseMigration
{
    public function up()
    {

        $this->addColumn('catCategory','type','int');

        return true;
    }

    public function down()
    {
        $this->removeColumn('catCategory','type');
        return true;
    }

    public function getDescription()
    {
        return "Добавление поля type в catCategory";
    }

    public function isConfirmed($returnBoolean = false){

        return $this->columnExist('catCategory','type');
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