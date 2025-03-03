<?php

class m1740904914_modif_add extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true) == true) return false;

        // Добавляем столбец type
        $this->addColumn('catItemsToItems', 'type', 'VARCHAR(50) NULL');

        // Обновляем существующие записи
        $sql = "UPDATE catItemsToItems SET type = 'option' WHERE type IS NULL";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        // Удаляем столбец type
        $this->removeColumn('catItemsToItems', 'type');

        return true;
    }

    public function getDescription()
    {
        return "Добавление столбца type в таблицу catItemsToItems и установка значения 'option' для существующих записей";
    }

    public function isConfirmed($returnBoolean = false)
    {

        $columnExists = $this->columnExist('catItemsToItems', 'type');

        if ($returnBoolean) {
            return $columnExists;
        }
        return $columnExists;
    }
}