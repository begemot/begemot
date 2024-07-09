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

                'actions' => array('newSchemaInstance', 'massDataProcess', 'NewFieldFromMd', 'massFieldImportFromMd'),

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

    public function actionNewFieldFromMd()
    {
        $this->render('newFieldFromMd');
    }

    public function parseMdTable($mdTable)
    {

        $rows = explode("\n", trim($mdTable));


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
        return $table;
    }

    public function actionMassFieldImportFromMd()
    {
        $params = json_decode(file_get_contents('php://input'), true);
        print_r($params);



        // Проверяем, что данные правильно получены
        if (!$params || !isset($params['message'])) {
            echo "No message data received!";
            return;
        }


        $table = $this->parseMdTable($params['message']);

        Yii::import('schema.models.SchemaField');
        $order = 0;
        foreach ($table as $row) {

            
            foreach ($row as $key => $value) {
                if ($value) {
                    $fieldModekExist = SchemaField::model()->findByAttributes(['name' => $value]);
                    if ($fieldModekExist) {
                        $scemaField = $fieldModekExist;
                    } else {
                        $scemaField = new SchemaField();
                    }

                    $order++;
                    $scemaField->schemaId = 1;
                    $scemaField->name = $value;
                    $scemaField->type = 'String';
                    $scemaField->order = $order;
                    $scemaField->save();
                    // $CSchmVehicle = new CSchmVehicle(null, 'Название', $key);
                    // $CSchmVehicle->set($fieldName, $value);
                }
            }
        }
        return;
    }

    public function actionMassDataProcess()
    {
        Yii::import('webroot.protected.models.CSchmVehicle');


        $params = json_decode(file_get_contents('php://input'), true);
        print_r($params);



        // Проверяем, что данные правильно получены
        if (!$params || !isset($params['message'])) {
            echo "No message data received!";
            return;
        }


        $table = $this->parseMdTable($params['message']);


        print_r($table);
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
