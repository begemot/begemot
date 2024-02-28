<?php


class PanelController extends Controller
{
    public $menu = [
        [
            'title'=>'Работа с изображениями',
            'href'=>'#'
        ]
    ];

    public $layout = 'begemot.views.layouts.bs5clearLayout';
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'ajaxOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array(
                    'massImageResize'),

                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionMassImageResize(){

        $this->render('massImageResize');
    }

}