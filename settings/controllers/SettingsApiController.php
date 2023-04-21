<?php


class SettingsApiController extends CController
{

    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('index','view'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('create','update'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('admin','delete'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionGetBoolean($moduleName, $paramName)
    {
        $settings = new CSettings($moduleName);
        $value = $settings->getSettingBoolean($paramName);
        if ($value !== null) {
            $response = array('success' => true, 'value' => $value);
        } else {
            $response = array('success' => false, 'error' => 'Parameter not found');
        }
        echo CJSON::encode($response);
    }

    public function actionSetBoolean($moduleName, $paramName)
    {
        $params = json_decode(file_get_contents('php://input'),true);
   
        $value = $params['value'];

        $settings = new CSettings($moduleName);
        $settings->setSettingBoolean($paramName, $value);
        $response = array('success' => true);
        echo CJSON::encode($response);
    }

    public function actionGetAll($moduleName)
    {
        $request = Yii::app()->getRequest();

        $settings = new CSettings($moduleName);

        echo CJSON::encode($settings->settings);
    }
}
