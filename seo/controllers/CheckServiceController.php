<?php

class CheckServiceController extends Controller
{
    public $layout = '';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array( 'textRuCallBack'),
                'users'=>array('*'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('index'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {

        $this->renderPartial('index');
    }

    public function actionTextRuCallBack()
    {
        if (isset($_REQUEST['uid'])){
            $uid = $_REQUEST['uid'];
            $seoCheck = SeoCheck::model()->findByAttributes(['uid' => $uid]);
            if (!$seoCheck) {
                $seoCheck = new SeoCheck();
                $seoCheck->uid = $uid;
            }
            $seoCheck->getCheckResult();
//            file_put_contents('./file.txt', json_encode($_POST), FILE_APPEND);
            echo 'ok';
         } else {
            echo 'no';
        }

    }

    public function actionJsonCheckResult($uid){

    }


}


