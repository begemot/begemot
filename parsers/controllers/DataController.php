<?php

class DataController extends Controller
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

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array( 'webParser','webParserProcess','webParserScenarioTask','webParserData'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionWebParserProcess()
    {
        $model=new WebParser('search');


        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['WebParser']))
            $model->attributes=$_GET['WebParser'];

        $this->render('webParser',array(
            'model'=>$model,
        ));
    }

    public function actionWebParserScenarioTask()
    {
        $model=new WebParserScenarioTask('search');
        $model->unsetAttributes();  // clear any default values
        $model->processId=$this->getLastProcessId();

        if(isset($_GET['WebParserScenarioTask']))
            $model->attributes=$_GET['WebParserScenarioTask'];

        $this->render('WebParserScenarioTask',array(
            'model'=>$model,
        ));
    }

    public function actionWebParserData()
    {
        $model=new WebParserData('search');
        $model->unsetAttributes();  // clear any default values
        $model->processId=$this->getLastProcessId();

        if(isset($_GET['WebParserData']))
            $model->attributes=$_GET['WebParserData'];

        $this->render('WebParserData',array(
            'model'=>$model,
        ));
    }

    private function getLastProcessId(){
        return Yii::app()->db->createCommand("SELECT max(id) from webParser")->queryScalar();
    }

}
