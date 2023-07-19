<?php

class DefaultController extends Controller
{
    public $layout = 'begemot.views.layouts.column2';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('index', 'newFile', 'delete', 'update'),
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



    public function actionNewFile()
    {
        $model = new NewFile();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'new-file-test-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }


        if (isset($_POST['NewFile'])) {
            $model->attributes = $_POST['NewFile'];
            if ($model->validate()) {

                $webroot = Yii::getPathOfAlias('webroot');
                $file = fopen($webroot . '/protected/views/site/pages/' . $model->filename . '.php', 'w');
                fclose($file);
                @chmod($webroot . '/protected/views/site/pages/' . $model->filename . '.php', 0777);

                $this->redirect('/pages');
                return;
            }
        }
        $this->render('newFile', array('model' => $model));
    }

    public function actionDelete($file)
    {
        $file = str_replace("*", "/", $file);

        unlink($file . '.php');
    }

    public function actionUpdate($file)
    {

        $model = new updateForm($file . '.php');


        if (isset($_POST['ajax']) && $_POST['ajax'] === 'update-form-update-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }


        if (isset($_POST['updateForm'])) {

            $model->attributes = $_POST['updateForm'];
            if ($model->validate()) {
                $model->saveFile();

            }
        }
        $this->render('update', array('model' => $model));
    }
}


