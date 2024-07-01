<?php

class m1719844643_standart_categories_create extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true) == true) return false;

        $sql = "
        INSERT INTO catCategory
        (id, pid, name, `text`, `order`, dateCreate, dateUpdate, status, name_t, `level`, seo_title, layout, viewFile, itemViewFile, published, `type`)
        VALUES
        (NULL, -1, 'archive', NULL, 4, NULL, NULL, 1, 'archive', 0, 'Модели снятые с производства', '', '', '', 1, NULL),
        (NULL, -1, 'catalog', NULL, 2, NULL, NULL, 1, 'catalog', 0, 'Каталог моделей вездеходов', '', '', '', 1, NULL),
        (NULL, -1, 'stock', NULL, 1, NULL, NULL, 1, 'stock', 0, 'Модели в наличии', '', '', '', 1, NULL),
        (NULL, -1, 'sold', NULL, 3, NULL, NULL, 1, 'sold', 0, 'Модели проданные из наличия', '', '', '', 1, NULL);
        ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "
        DELETE FROM catCategory WHERE name IN ('archive', 'catalog', 'stock', 'sold');
        ";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Добавление стандартных разделов в catCategory";
    }

    public function isConfirmed($returnBoolean = false)
    {
        // Проверяем наличие всех записей с name 'archive', 'catalog', 'stock', 'sold'
        return $this->tableExist('catCategory') && 
               $this->existsInTable('catCategory', ['archive', 'catalog', 'stock', 'sold']);
    }

    private function existsInTable($table, $names)
    {
        foreach ($names as $name) {
            $result = Yii::app()->db->createCommand()
                ->select('id')  // Используем существующую колонку, например, 'id'
                ->from($table)
                ->where('name=:name', [':name' => $name])
                ->queryScalar();
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
