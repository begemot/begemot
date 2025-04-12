<?php

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
    public function accessRules()
    {
        return array(

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array('index'),

                'expression' => 'Yii::app()->user->canDo()'


            ),
            array(
                'deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionIndex()
    {
        $this->render('index');
    }
}
