<?php

class m1717582737_SchemaField_UoF_id_add extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true)) {
            return false;
        }

        $sql = "ALTER TABLE `SchemaField`
                ADD COLUMN `UoFId` INT(11) DEFAULT NULL;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if (!$this->isConfirmed(true)) {
            return false;
        }

        $sql = "ALTER TABLE `SchemaField`
                DROP COLUMN `UoFId`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Добавление столбца UoFId в таблицу SchemaField";
    }

    public function isConfirmed($returnBoolean = false)
    {
        $tableExists = $this->tableExist('SchemaField');
        $columnExists = $this->columnExist('SchemaField', 'UoFId');

        if ($returnBoolean) {
            return $tableExists && $columnExists;
        }

        return $tableExists && $columnExists ? "Колонка UoFId существует в таблице SchemaField" : false;
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
