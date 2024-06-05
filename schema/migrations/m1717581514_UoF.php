<?php

class m1717581514_UoF extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true)) {
            return false;
        }

        $sql = "CREATE TABLE `SchemaUnitOfMeasurement` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL UNIQUE,  -- Enforce unique unit names
            `abbreviation` VARCHAR(20) DEFAULT NULL,  -- Optional abbreviation for the unit
            `description` TEXT DEFAULT NULL,        -- Optional description of the unit
            PRIMARY KEY (`id`),
            UNIQUE KEY `unit_name_unique` (`name`)  -- Additional unique key on name for faster lookups
        ) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if (!$this->isConfirmed(true)) {
            return false;
        }

        $sql = "DROP TABLE `SchemaUnitOfMeasurement`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Создание таблицы SchemaUnitOfMeasurement";
    }

    public function isConfirmed($returnBoolean = false)
    {
        return $this->tableExist('SchemaUnitOfMeasurement');
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
