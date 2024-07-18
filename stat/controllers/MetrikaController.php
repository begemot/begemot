<?php
Yii::import('stat.models.*');
class MetrikaController extends Controller
{
    public $layout = 'begemot.views.layouts.bs5clearLayout';

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionList()
    {
        $metrikas = Metrika::model()->findAll();
        echo CJSON::encode($metrikas);
        Yii::app()->end();
    }

    public function actionCreate()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $metrika = new Metrika;
        $metrika->attributes = $request;
        if ($metrika->save()) {
            echo CJSON::encode(['status' => 'success']);
        } else {
            echo CJSON::encode(['status' => 'error', 'errors' => $metrika->getErrors()]);
        }
        Yii::app()->end();
    }

    public function actionUpdate($id)
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $metrika = Metrika::model()->findByPk($id);
        if ($metrika) {
            $metrika->attributes = $request;
            if ($metrika->save()) {
                echo CJSON::encode(['status' => 'success']);
            } else {
                echo CJSON::encode(['status' => 'error', 'errors' => $metrika->getErrors()]);
            }
        } else {
            echo CJSON::encode(['status' => 'error', 'message' => 'Модель не найдена']);
        }
        Yii::app()->end();
    }

    public function actionDelete($id)
    {
        $metrika = Metrika::model()->findByPk($id);
        if ($metrika) {
            $metrika->delete();
            echo CJSON::encode(['status' => 'success']);
        } else {
            echo CJSON::encode(['status' => 'error', 'message' => 'Модель не найдена']);
        }
        Yii::app()->end();
    }
}
