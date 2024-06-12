<?php

class CatalogAndSchemaController extends Controller
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
                    'Index', 'attachSchema'
                ),


                'expression' => 'Yii::app()->user->canDo("*")'
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }
    public $layout = 'begemot.views.layouts.bs5clearLayout';

    public function actionIndex()
    {
        $this->layout = 'begemot.views.layouts.bs5clearLayout';
        $this->render('index');
    }

    public function actionAttachSchema()
    {
        Yii::import('schema.models.SchemaLinks');
        Yii::import('schema.components.ApiFunctions');
        // Assuming you're using PHP
        $data = json_decode(file_get_contents('php://input'), true);
        $catItemsdata = $data['selectedItems'];
        print_r($data);

        foreach ($catItemsdata as $itemData) {

            $res = SchemaLinks::model()->findAllByAttributes([
                'linkId' => $itemData['id'],
                'linkType' => 'CatItem',
                'schemaId' => $data['selectedSchemaLink']['schemaId']
            ]);

            if (!$res) {
                $newSchema = new SchemaLinks();
                $newSchema->linkId = $itemData['id'];
                $newSchema->linkType = 'CatItem';
                $newSchema->schemaId = $data['selectedSchemaLink']['schemaId'];
                if (!$newSchema->save()) {
                    throw new Exception('Не удалось создать схему!');
                } else {
                    $res = $newSchema;
                }
            } else {
                $res = array_shift($res);
            }

            Yii::import('schema.components.CSchemaLink');

            $CSchemaLinks = new CSchemaLink('CatItem', $res->linkId, $data['selectedSchemaLink']['schemaId']);

            $url = Yii::app()->createAbsoluteUrl('/schema/api/GetSchemaData');
            $schemaData = ApiFunctions::getLineSchemaData($data['selectedSchemaLink']['linkType'], $data['selectedSchemaLink']['linkId']);

            foreach ($schemaData as $fieldName => $schemaDataAndValue) {
                //  print_r($fieldName);
                //  $res->set($fieldName, $schemaDataAndValue['value']);
                $CSchemaLInk = new CSchemaLink($res->linkType, $res->linkId, $res->schemaId);
                $CSchemaLInk->set($fieldName, $schemaDataAndValue['value'], $res->linkType, $schemaDataAndValue['type']);
            }



            // Use file_get_contents to fetch the JSON data
            // $json = file_get_contents($url . '?linkType=' . $data['selectedSchemaLink']['linkType'] . '&linkId=' . $data['selectedSchemaLink']['linkId']);


        }

        // SchemaLinks::model()->findAllByAttributes($data);
        // $selectedItems = $data['selectedItems'];
        // $selectedSchemaLink = $data['selectedSchemaLink'];

        // Now you can use $selectedItems and $selectedSchemaLink as needed

        // $this->renderPartial('attachSchema', ['data' => $data]);
    }
}