<?php

class ManageController extends Controller
{
    public $layout = 'begemot.views.layouts.column2';


    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }
    public function accessRules()
    {
        return array(

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array('newSchemaInstance', 'massDataProcess'),

                'expression' => 'Yii::app()->user->canDo()'


            ),
            array(
                'deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionNewSchemaInstance()
    {
        $this->render('newSchemaInstance');
    }

    public function actionMassDataProcess()
    {
        Yii::import('webroot.protected.models.CSchmVehicle');


        // Получаем данные из запроса
        $params = json_decode(file_get_contents('php://input'), true);
        print_r($params);
        // Проверяем, что данные правильно получены
        if (!$params || !isset($params['message'])) {
            echo "No message data received!";
            return;
        }

        // Разделение строки на массив строк по новой строке
        $rows = explode("\n", trim($params['message']));

        // // Удаляем разделители таблицы
        // $rows = array_filter($rows, function ($row) {
        //     return !preg_match('/^-+$/', trim($row));
        // });

        // Парсинг строк
        $headers = [];
        $table = [];
        foreach ($rows as $index => $row) {
            $columns = array_map('trim', explode('|', trim($row)));
            // Удаляем пустые элементы, которые могут появиться из-за пробелов
            $columns = array_filter($columns, function ($column) {
                return $column !== "";
            });

            // if (preg_match('/^-+$/', array_shift($columns))) {
            //     continue;
            // }

            if ($index == 1) continue;

            if ($index == 0) {
                $headers = $columns;
            } else {
                if (count($columns) == count($headers)) {
                    $table[] = array_combine($headers, $columns);
                }
            }
        }
        print_r($table);


        // print_r($table);
        foreach ($table as $row) {
            $fieldName = $row['Характеристика'];
            unset($row['Характеристика']);
            foreach ($row as $key => $value) {
                if ($value) {
                    print_r($key);
                    $CSchmVehicle = new CSchmVehicle(null, 'Название', $key);
                    $CSchmVehicle->set($fieldName, $value);
                }
            }
        }
    }
}