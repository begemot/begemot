<?php

class SchemaDataController extends Controller
{

    public $layout = 'begemot.views.layouts.bs5clearLayout';


    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl'
        );
    }
    public function accessRules()
    {
        return array(

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array('update', 'admin'),

                'expression' => 'Yii::app()->user->canDo()'


            ),
            array(
                'deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionAdmin()
    {
        // Получаем параметры пагинации из запроса
        $page = (int)Yii::app()->request->getParam('page', 1);
        $perPage = (int)Yii::app()->request->getParam('perPage', 10);

        // Рассчитываем смещение
        $skip = ($page - 1) * $perPage;

        // Получаем коллекцию
        $collection = Yii::app()->mongoDb->getCollection('schemaData');

        // Определяем какие поля выбирать
        $projection = [
            'groupId' => 1,
            'linkType' => 1,
            'schemaId' => 1,
            'fields.Название' => 1,
            'fields.name' => 1
            // добавьте другие нужные поля
        ];

        // Опции запроса с пагинацией
        $options = [
            'projection' => $projection,
            'skip' => $skip,
            'limit' => $perPage,

            // можно добавить сортировку:
            // 'sort' => ['groupId' => 1]
        ];

        // Получаем данные с пагинацией
        $cursor = $collection->find([], $options);
        $data = iterator_to_array($cursor); // или foreach по курсору

        // Получаем общее количество документов для пагинации
        $totalCount = $collection->count([]);

        // Рассчитываем общее количество страниц
        $totalPages = ceil($totalCount / $perPage);

        // Формируем ответ
        $data = [
            'data' => $data,
            'pagination' => [
                'totalItems' => $totalCount,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'perPage' => $perPage,
                'hasNextPage' => $page < $totalPages,
                'hasPrevPage' => $page > 1
            ]
        ];

        $this->render('admin', ['data' => $data]);
    }


    public function actionUpdate($id)
    {



        // if (isset($_POST['SchemaLinks'])) {
        //     $model->attributes = $_POST['SchemaLinks'];
        //     if ($model->save())
        //         $this->redirect(array('update', 'id' => $model->id));
        // }

        $this->render('update', array(
            'id' => $id,
        ));
    }
}
