<?php

class m1717698981_schema_link_name extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE SchemaLinks ADD COLUMN name varchar(100) NULL;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE SchemaLinks DROP COLUMN name;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Adds 'name' field to SchemaLinks table";
    }

    public function isConfirmed($returnBoolean = false)
    {
        return $this->tableExist('SchemaLinks') && $this->columnExist('SchemaLinks', 'name');
    }



    /*
     * Use safeUp/safeDown to do migration with transaction
     * public function safeUp()
     * {
     * }
     * public function safeDown()
     * {
     * }
     */
}