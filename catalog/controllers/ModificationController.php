<?php

class ModificationController extends CController
{
    public function filters()
    {
        return array(
            'postOnly + sync',
        );
    }

    // Получение списка модификаций для itemId
    public function actionList($itemId)
    {
        $modifications = CatItemsToItems::model()->findAll(array(
            'condition' => 'itemId = :itemId AND type = "modification"',
            'params' => array(':itemId' => $itemId),

            'with' => array('toItem'),
        ));

        $result = array();
        foreach ($modifications as $modification) {
            $result[] = array(
                'id' => $modification->id,
                'itemId' => $modification->itemId,
                'toItemId' => $modification->toItemId,
                'order' => $modification->order,
                'toItemName' => $modification->toItem ? $modification->toItem->name : null,
            );
        }

        $this->sendResponse(200, json_encode($result));
    }

    // Предполагаемая структура контроллера
    public function actionSync()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['itemId']) || !isset($data['modifications'])) {
            $this->sendResponse(400, json_encode(array(
                'error' => 'Missing required parameters: itemId and modifications'
            )));
            return;
        }

        $itemId = $data['itemId'];
        $newModifications = $data['modifications'];

        // Получаем текущие модификации из БД
        $existingMods = CatItemsToItems::model()->findAll(array(
            'condition' => 'itemId = :itemId AND type = "modification"',
            'params' => array(':itemId' => $itemId),
        ));

        // Получаем менеджер категорий
        Yii::import('catalog.components.CategoryManager');
        $categoryManager = new CategoryManager();


        if ($categoryManager === null) {
            $this->sendResponse(500, json_encode(['error' => 'CategoryManager component not initialized']));
            return;
        }
        $standardCategories = $categoryManager->getStandardCategories();

        // Преобразуем входные данные в массив toItemId для удобства
        $newToItemIds = array_column($newModifications, 'id');
        $existingToItemIds = array_column($existingMods, 'toItemId');

        // Обрабатываем открепленные модификации
        foreach ($existingMods as $existingMod) {
            if (!in_array($existingMod->toItemId, $newToItemIds)) {
                // Удаляем связь модификации
                $existingMod->delete();
                // Перемещаем открепленную позицию в Catalog
                $categoryManager->moveToStandardCategory($existingMod->toItemId, 'catalog');
            }
        }

        $result = array();
        $order = 0;

        // Добавляем/обновляем модификации
        foreach ($newModifications as $modData) {
            $toItemId = $modData['id'];

            // Проверяем, существует ли уже такая связь
            $existing = CatItemsToItems::model()->findByAttributes(array(
                'itemId' => $itemId,
                'toItemId' => $toItemId,
                'type' => 'modification'
            ));

            if ($existing) {
                // Обновляем порядок если нужно
                $existing->order = $order;
                $existing->save();
                $model = $existing;
            } else {
                // Создаем новую связь
                $model = new CatItemsToItems();
                $model->itemId = $itemId;
                $model->toItemId = $toItemId;
                $model->type = 'modification';
                $model->order = $order;
                $model->save();

                // Прикрепляем позицию к Modifications, убирая из других категорий
                $categoryManager->moveToStandardCategory($toItemId, 'modifications');
            }

            $result[] = array(
                'id' => $model->id,
                'itemId' => $model->itemId,
                'toItemId' => $model->toItemId,
                'order' => $model->order,
                'name' => $modData['name'],
                'image' => $modData['image'],
                'article' => $modData['article']
            );

            $order++;
        }

        $this->sendResponse(200, json_encode(array(
            'success' => true,
            'modifications' => $result
        )));
    }

    private function sendResponse($status, $body)
    {
        header('Content-Type: application/json');
        header('HTTP/1.1 ' . $status);
        echo $body;
        Yii::app()->end();
    }
}