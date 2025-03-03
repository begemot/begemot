<?php

class CategoryManager extends CComponent
{
    // Стандартные категории
    private $standardCategories = [
        'catalog' => 'Catalog',
        'stock' => 'Stock',
        'archive' => 'Archive',
        'modifications' => 'Modification',
        'sold' => 'Sold'
    ];

    /**
     * Получить список стандартных категорий и их ID
     * @return array ['alias' => ['id' => int, 'name' => string]]
     */
    public function getStandardCategories()
    {
        $result = [];
        foreach ($this->standardCategories as $alias => $name) {
            $category = CatCategory::model()->findByAttributes(['name' => $name]);
            $result[$alias] = [
                'id' => $category ? $category->id : null,
                'name' => $name
            ];
        }
        return $result;
    }

    /**
     * Прикрепить catItem к категории, сохраняя существующие связи
     * @param int $itemId ID элемента
     * @param int $catId ID категории
     * @param int $order Порядок (по умолчанию 0)
     * @return bool Успешность операции
     */
    public function attachToCategory($itemId, $catId, $order = 0)
    {
        // Проверяем, существует ли уже связь
        $existing = CatItemsToCat::model()->findByAttributes([
            'itemId' => $itemId,
            'catId' => $catId
        ]);

        if ($existing) {
            return true; // Связь уже существует
        }

        $model = new CatItemsToCat();
        $model->itemId = $itemId;
        $model->catId = $catId;
        $model->order = $order;
        $model->through_display = 0;
        $model->is_through_display_child = 0;
        $model->through_display_count = null;

        return $model->save();
    }

    /**
     * Переместить catItem в категорию, удалив из других
     * @param int $itemId ID элемента
     * @param int $catId ID категории
     * @param int $order Порядок (по умолчанию 0)
     * @return bool Успешность операции
     */
    public function moveToCategory($itemId, $catId, $order = 0)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            // Удаляем все существующие связи
            CatItemsToCat::model()->deleteAllByAttributes([
                'itemId' => $itemId
            ]);

            // Создаем новую связь
            $model = new CatItemsToCat();
            $model->itemId = $itemId;
            $model->catId = $catId;
            $model->order = $order;
            $model->through_display = 0;
            $model->is_through_display_child = 0;
            $model->through_display_count = null;

            if (!$model->save()) {
                throw new Exception('Failed to save new relation');
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollback();
            return false;
        }
    }

    /**
     * Переместить catItem в стандартную категорию, удалив из других
     * @param int $itemId ID элемента
     * @param string $categoryAlias Алиас стандартной категории (catalog, stock, etc.)
     * @param int $order Порядок (по умолчанию 0)
     * @return bool Успешность операции
     */
    public function moveToStandardCategory($itemId, $categoryAlias, $order = 0)
    {
        if (!array_key_exists($categoryAlias, $this->standardCategories)) {
            return false;
        }

        $category = CatCategory::model()->findByAttributes([
            'name' => $this->standardCategories[$categoryAlias]
        ]);

        if (!$category) {
            return false; // Категория не найдена
        }

        return $this->moveToCategory($itemId, $category->id, $order);
    }

    /**
     * Прикрепить catItem к стандартной категории, сохраняя существующие связи
     * @param int $itemId ID элемента
     * @param string $categoryAlias Алиас стандартной категории (catalog, stock, etc.)
     * @param int $order Порядок (по умолчанию 0)
     * @return bool Успешность операции
     */
    public function attachToStandardCategory($itemId, $categoryAlias, $order = 0)
    {
        if (!array_key_exists($categoryAlias, $this->standardCategories)) {
            return false;
        }

        $category = CatCategory::model()->findByAttributes([
            'name' => $this->standardCategories[$categoryAlias]
        ]);

        if (!$category) {
            return false; // Категория не найдена
        }

        return $this->attachToCategory($itemId, $category->id, $order);
    }
}