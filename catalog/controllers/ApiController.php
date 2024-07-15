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
                    'itemListJson', 'GetCategoriesOfCatItem', 'MoveItemsToStandartCat', 'GetCatList','massItemsMoveToCats'
                ),


                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
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
            $criteria->compare('t.name', $name, true);
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
                'image' => $pBox->getFirstImage('admin')
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

    public function actionMassItemsMoveToCats(){
       
        // Получаем параметр name из GET запроса
         $rawPostData = file_get_contents("php://input");
         $data = CJSON::decode($rawPostData, true);

         if ( !isset($data['selectedCats']) && !isset($data['selectedItems']) ){
            throw new Exception('нет данных');
         }  else{

            $itemsIds = array_column($data['selectedItems'],'id');
            $catItems = CatItem::model()->findAllByAttributes(['id'=>$itemsIds]);
            foreach ($catItems as $catItem){
                foreach ($data['selectedCats'] as $catId){
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


    public function actionGetOptionslist($itemId){

    }
}