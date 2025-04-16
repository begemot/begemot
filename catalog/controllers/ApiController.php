<?php

Yii::import('webroot.protected.jobs.*');

class ApiController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations

        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions


                'actions' => array(
                    'GetOptionslist',
                    'itemListJson',
                    'GetCategoriesOfCatItem',
                    'MoveItemsToStandartCat',
                    'GetCatList',
                    'massItemsMoveToCats',
                    'MassOptionsImport',
                    'massItemImageImport',
                    'DeleteOption',
                    'ExportImagesOfCatItem',
                    'CatItemAttributesSet'
                ),


                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionExportImagesOfCatItem($id)
    {
        // $id = 513;

        Yii::import('pictureBox.components.PBox');
        $pBox = new PBox('catalogItem', $id);
        $array = array_column($pBox->getSortedImageList(), 'original');
        $resultArray = [];
        $host = $_SERVER['HTTP_HOST'];
        foreach ($array as $key => $elem) {
            $resultArray[] = ['url' => 'https://' . $host . $elem];
        }
        echo json_encode($resultArray);
    }

    public function actionItemListJson()
    {
        // Получаем параметр name из GET запроса
        $rawPostData = file_get_contents("php://input");
        $data = CJSON::decode($rawPostData, true);

        $name = isset($data['name']) ? $data['name'] : '';
        $categoryIds = isset($data['catFilterIds']) ? $data['catFilterIds'] : [];

        // // Проверка входящих данных
        // if (empty($categoryIds)) {
        //     echo CJSON::encode([]);
        //     Yii::app()->end();
        // }

        // Создаем критерий для поиска
        $criteria = new CDbCriteria;
        if (is_array($categoryIds) && count($categoryIds) > 0) {
            $catItemToCatModels = CatItemsToCat::model()->findAllByAttributes(['catId' => $categoryIds]);
            // Преобразуем модели в массивы
            $catItemToCatArray = [];
            foreach ($catItemToCatModels as $model) {
                $catItemToCatArray[] = $model->attributes;
            }
            $itemIds = array_column($catItemToCatArray, 'itemId');
            $criteria->addInCondition('id', $itemIds);
            // $criteria->with = array('categories');
            // $criteria->together = true;
            // $criteria->addInCondition('categories.id', $categoryIds);
            // $criteria->group = 't.id';
            //$criteria->having = 'COUNT(DISTINCT categories.id) = ' . count($categoryIds);
        }


        // Если параметр name не пустой, добавляем условие фильтрации
        if (!empty($name)) {
            //  $criteria->compare('name', $name, true);
            // $criteria->compare('article', $name, true);
            $criteria->addCondition('name LIKE :name OR article LIKE :article');
            $criteria->params[':name'] = '%' . $name . '%';
            $criteria->params[':article'] = '%' . $name . '%';
        }


        // Находим все подходящие записи
        $catItems = CatItem::model()->findAll($criteria);

        // Преобразуем записи в массив для вывода в формате JSON, оставляем только name и id
        $result = [];
        foreach ($catItems as $item) {
            Yii::import('pictureBox.components.PBox');
            $pBox = new PBox('catalogItem', $item->id);

            $result[] = [
                'id' => $item->id,
                'name' => $item->name,
                'image' => $pBox->getFirstImage('admin'),
                'article' => $item->article
            ];
        }

        // Выводим результат в формате JSON
        header('Content-Type: application/json');
        echo CJSON::encode($result);
        Yii::app()->end();
    }

    public function actionGetCategoriesOfCatItem($itemId)
    {
        // Поиск всех записей catItemsToCat по itemId
        $catItemsToCat = CatItemsToCat::model()->findAllByAttributes(['itemId' => $itemId]);

        // Массив для хранения данных категорий
        $categories = [];

        // Проход по всем найденным записям и добавление данных категорий в массив
        foreach ($catItemsToCat as $item) {
            $categories[] = $item->cat->attributes; // Добавляем все атрибуты связанной категории
        }

        // Вывод данных в формате JSON
        echo CJSON::encode($categories);
    }
    public function actionMoveItemsToStandartCat()
    {
        $rawPostData = file_get_contents("php://input");
        $data = CJSON::decode($rawPostData, true);

        $selectedItems = isset($data['selectedItems']) ? $data['selectedItems'] : [];
        $name = $data['where'];
        if (!empty($selectedItems)) {
            // Ваша логика для перемещения элементов на склад
            foreach ($selectedItems as $selectedItem) {
                CatItem::model()->findByPk($selectedItem['id'])->moveToStandartCat($name);
            }
        }

        // Возврат ответа
        echo CJSON::encode(['status' => 'success', 'message' => 'Items moved to stock successfully.']);
        Yii::app()->end();
    }

    public function actionMassItemsMoveToCats()
    {

        // Получаем параметр name из GET запроса
        $rawPostData = file_get_contents("php://input");
        $data = CJSON::decode($rawPostData, true);

        if (!isset($data['selectedCats']) || !isset($data['selectedItems'])) {
            throw new Exception('нет данных');
        } else {



            $itemsIds = array_column($data['selectedItems'], 'id');
            $catItems = CatItem::model()->findAllByAttributes(['id' => $itemsIds]);
            foreach ($catItems as $catItem) {
                foreach ($data['selectedCats'] as $catId) {

                    if (isset($data['deleteAllCats']) && $data['deleteAllCats'] == true) {
                        $catitemsToCatModels = CatItemsToCat::model()->findAllByAttributes(['itemId' => $catItem->id]);

                        foreach ($catitemsToCatModels as $catitemsToCatModel) {
                            $catitemsToCatModel->delete();
                        }
                    }

                    $catItem->moveToCat($catId);
                }
            }
        }
    }

    public function actionGetCatList()
    {
        $model = CatCategory::model();
        //$tmp = $model->getcategoriesTree();
        $model->loadCategories();
        $tmp = $model->categories;



        echo json_encode($tmp);
    }


    public function actionGetOptionslist($itemId)
    {
        // Найти все записи, где itemId равен переданному значению
        $options = CatItemsToItems::model()->findAllByAttributes(array('itemId' => $itemId, 'type' => 'option'));

        // Если записи найдены, подготовить данные для вывода
        if ($options) {
            $result = array();
            foreach ($options as $option) {
                // Загрузить модель CatItem по toItemId
                $catItem = CatItem::model()->findByPk($option->toItemId);
                if ($catItem) {
                    $result[] = array(
                        'itemId' => $option->toItemId,

                        'name' => $catItem->name,
                        'price' => $catItem->price,
                        'isBase' => $option->isBase,  // добавить isBase
                        'url' => 'https://' . $_SERVER['HTTP_HOST'] . $catItem->getPbox()->getFirstImage('admin')
                    );
                }
            }

            // Выводим JSON
            echo CJSON::encode($result);
        } else {
            // Если записи не найдены, выводим пустой массив
            echo CJSON::encode(array());
        }
    }

    public function actionDeleteOption()
    {
        $jsonoptions = file_get_contents("php://input");
        $data = CJSON::decode($jsonoptions, true);
        $data = $data['params'];
        $itemId = $data['itemId'];
        $optionId = $data['option']['itemId'];

        Yii::import('catalog.models.*');
        $catItemsToItems = CatItemsToItems::model()->findAllByAttributes(['itemId' => $itemId, 'toItemId' => $optionId]);
        $catItemOption = CatItem::model()->findByPk($optionId);
        if ($catItemOption) {
            $catItemOption->delete();
            foreach ($catItemsToItems as $catItemsToItem) {
                $catItemsToItem->delete();
            }
        }
    }

    public function actionMassOptionsImport()
    {

        $jsonoptions = file_get_contents("php://input");
        $data = CJSON::decode($jsonoptions, true);

        // Проверяем, что данные были получены и декодированы
        if (!$data) {
            throw new CHttpException(400, 'Invalid JSON input.');
        }

        // Проверяем, что основные ключи присутствуют
        if (!isset($data['data']) || !isset($data['additionalData'])) {
            throw new CHttpException(400, 'Missing required data.');
        }

        // Предполагаем, что у нас есть идентификатор основного элемента в additionalData
        $mainItemId = $data['additionalData']['id'];

        // Ищем основной элемент CatItem по идентификатору
        $mainItem = CatItem::model()->findByPk($mainItemId);

        if (!$mainItem) {
            throw new CHttpException(404, 'Main item not found.');
        }

        // Создание и связывание опций
        foreach ($data['data'] as $itemData) {


            $isExist = false;

            if (isset($itemData['article']) || isset($itemData['itemId'])) {

                if (isset($itemData['article'])) {

                    $res = CatItem::model()->findAllByAttributes(['article' => $itemData['article']]);
                }

                if (isset($itemData['itemId'])) {

                    $res = CatItem::model()->findAllByAttributes(['id' => $itemData['itemId']]);
                }

                if (is_array($res) && count($res) > 0) {
                    $isExist = true;
                }
            }

            if ($isExist) {

                $item = array_shift($res);
                $res2 = CatItemsToItems::model()->findAllByAttributes(['toItemId' => $item->id, 'itemId' => $mainItem->id]);

                if (!(is_array($res2) && count($res2) > 0)) {
                    $itemsToItems = new CatItemsToItems();
                    $itemsToItems->toItemId = $item->id;
                    $itemsToItems->itemId = $mainItem->id;
                    $itemsToItems->type = 'option';
                    if (isset($itemData['isbase']) && $itemData['isbase'] == 1) {
                        $itemsToItems->isBase = 1;
                    }

                    if (!$itemsToItems->save()) {
                        throw new CHttpException(500, 'Failed to save item-to-item link.');
                    }
                } else {

                    $itemsToItems = array_shift($res2);

                    if (isset($itemData['isbase']) && $itemData['isbase'] == 1) {
                        $itemsToItems->isBase = 1;
                        $itemsToItems->save();
                    }
                }

                $category = CatCategory::model()->find('name=:name', array(':name' => 'options'));
                if ($category) {
                    $this->addCategory($item->id, $category->id);
                }
                // Связывание опции с основным CatItem
                // $itemsToItems = new CatItemsToItems();
                // $itemsToItems->toItemId = $item->id;
                // $itemsToItems->itemId = $mainItem->id;



            } else {
                $item = new CatItem();
                $item->name = $itemData['name'];
                $item->price = $itemData['price'];


                $item->status = 1;
                $item->data = json_encode($itemData);
                $item->quantity = 1;
                $item->delivery_date = time();
                $item->article = 'option-item';

                if (!$item->save()) {
                    throw new CHttpException(500, 'Failed to save option item.');
                } else {

                    Yii::import('pictureBox.components.PBox');

                    $pBox = new PBox('catalogItem', $item->id);


                    $picturesConfig = array();
                    if (isset($itemData['url'])){
                        $configFile = Yii::getPathOfAlias(CatalogModule::CAT_ITEM_CONFIG_FILE_ALIAS) . '.php';
                        if (file_exists($configFile)) {
                            Yii::import('pictureBox.components.PictureBoxFiles');
                            $defConf = PictureBoxFiles::getDefaultConfig();
                            $picturesConfig = require($configFile);
                            $picturesConfig = array_merge_recursive($defConf, $picturesConfig);
                        }
    
                        $pBox->filters = $picturesConfig;
                        $pBox->addImagefile($itemData['url']);
                    }


                    // Связывание опции с категорией (предполагаем, что категория "options" уже существует)
                    $category = CatCategory::model()->find('name=:name', array(':name' => 'options'));
                    if ($category) {
                        $this->addCategory($item->id, $category->id);
                    }

                    // Связывание опции с основным CatItem
                    $itemsToItems = new CatItemsToItems();
                    $itemsToItems->toItemId = $item->id;
                    $itemsToItems->itemId = $mainItem->id;
                    $itemsToItems->type = 'option';

                    if (isset($itemData['isBase']) && $itemData['isBase'] === '1') {
                        $itemsToItems->isBase = 1;
                    }

                    if (!$itemsToItems->save()) {
                        throw new CHttpException(500, 'Failed to save item-to-item link.');
                    }
                }
            }
        }
    }

    private function addCategory($itemId, $categoryId)
    {
        $categoryItem = new CatItemsToCat();
        $categoryItem->itemId = $itemId;
        $categoryItem->catId = $categoryId;
        $categoryItem->order = 0; // или любое другое значение по умолчанию

        if (!$categoryItem->save()) {
            throw new CHttpException(500, 'Failed to save category link.');
        }
    }

    public function actionMassItemImageImport()
    {
        $jsonoptions = file_get_contents("php://input");
        $data = CJSON::decode($jsonoptions, true);

        // Проверяем, что данные были получены и декодированы
        if (!$data) {
            throw new CHttpException(400, 'Invalid JSON input.');
        }
        // Проверяем, что основные ключи присутствуют
        if (!isset($data['data']) || !isset($data['additionalData'])) {
            throw new CHttpException(400, 'Missing required data.');
        }

        // Предполагаем, что у нас есть идентификатор основного элемента в additionalData
        $itemId = $data['additionalData']['id'];
        Yii::import('pictureBox.components.PBox');

        $configFile = Yii::getPathOfAlias(CatalogModule::CAT_ITEM_CONFIG_FILE_ALIAS) . '.php';
        if (file_exists($configFile)) {
            Yii::import('pictureBox.components.PictureBoxFiles');
            $defConf = PictureBoxFiles::getDefaultConfig();
            $picturesConfig = require($configFile);
            $picturesConfig = array_merge_recursive($defConf, $picturesConfig);
        }

        foreach ($data['data'] as $url) {
            $pBox = new PBox('catalogItem', $itemId);
            // while ($pBox->isLock()) {
            // sleep(1);
            // $pBox = new PBox('catalogItem', $itemId);
            // }
            // $pBox->setLock();

            $pBox->filters = $picturesConfig;
            $pBox->addImagefile($url['url']);
            $pBox->unLock();
            // sleep(1);
        }
    }

    public function actionCatItemAttributesSet()
    {
        // Проверка, является ли запрос AJAX
        if (!Yii::app()->request->isAjaxRequest) {
            throw new CHttpException(400, 'Invalid request.');
        }

        // Получение данных из POST-запроса
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);
        $data = json_decode($data['data'], true);

        Yii::import('catalog.models.CatItemsToItems');
        $model = CatItemsToItems::model()->findByAttributes([
            'itemId' => $data['baseItem']['id'],
            'toItemId' => $data['itemId']
        ]);
        print_r($data);
        $model->isBase = $data['attributes']['isBase'];

        // Сохранение изменений в базе данных
        if ($model->save()) {
            // Успешное сохранение
            echo CJSON::encode(['status' => 'success', 'message' => 'Attributes updated successfully.']);
        } else {
            // Ошибка при сохранении
            throw new CHttpException(500, 'Failed to update attributes.');
        }
    }
}
