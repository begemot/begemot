<?php
Yii::import('modules.components.ModulesManager');
class DefaultController extends Controller
{
    public $layout = 'begemot.views.layouts.bs5clearLayout';


    /**
     * @return array action filters
     */
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

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('getModulesList', 'index', 'getModulesDataList','activateModule','deactivateModule'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }



    public function actionIndex()
    {
        $this->render('index');
    }


    public function actionGetModulesDataList()
    {
        $data = ModulesManager::getModulesData();
        echo json_encode($data);
    }

    public function actionGetModulesList()
    {

        echo json_encode(ModulesManager::getModulesList());
    }

    public function actionActivateModule ($module){
        $data = ModulesManager::getModulesData();
        $data[$module]['active'] = true;
        ModulesManager::saveModulesData($data);
    }

    public function actionDeactivateModule ($module){
        $data = ModulesManager::getModulesData();
        $data[$module]['active'] = false;
        ModulesManager::saveModulesData($data);
    }
}