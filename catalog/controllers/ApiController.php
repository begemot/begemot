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
                    'itemListJson', 'GetCategoriesOfCatItem', 'MoveItemsToStandartCat', 'GetCatList'
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
        $name = Yii::app()->request->getQuery('name', '');

        // Создаем критерий для поиска
        $criteria = new CDbCriteria;

        // Если параметр name не пустой, добавляем условие фильтрации
        if (!empty($name)) {
            $criteria->compare('name', $name, true);
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

    public function actionGetCatList()
    {
        $model = CatCategory::model();
        //$tmp = $model->getcategoriesTree();
        $model->loadCategories();
        $tmp = $model->categories;



        echo json_encode($tmp);
    }
}
