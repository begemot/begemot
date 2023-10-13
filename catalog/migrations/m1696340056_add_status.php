<?php

class m1696340056_add_status extends BaseMigration
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE moshovercraft.NewTable (
	test varchar(100) NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb3
COLLATE=utf8mb3_general_ci;
";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `NewTable`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Добавление поля status в таблицу catItems";
    }

    public function isConfirmed($returnBoolean = false){

        return $this->columnExist('catItems','status');
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