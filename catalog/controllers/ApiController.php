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
                    'itemListJson'
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
}