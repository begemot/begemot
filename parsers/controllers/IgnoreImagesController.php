<?php

class IgnoreImagesController extends Controller
{
    public $layout='begemot.views.layouts.column2';
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
                'actions'=>array('create', 'delete'),
                'expression'=>'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionCreate()
    {

        if (isset($_POST['md5']) & isset($_POST['sha1']) & isset($_POST['image'])) {
            $md5 = $_POST['md5'];
            $sha1 = $_POST['sha1'];
            $image = $_POST['image'];
        
            $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/ignoredImages';

            if (!file_exists($dir))
                mkdir($dir, 0777);

            $file = $image;
            $temp = explode('.', $file);
            $imageExt = end($temp);

            $filePath = $dir . "/" . $md5 . '_' . $sha1 . '.' . $imageExt;

            copy($image, $filePath);

            $model = new ParsersIgnoreImages;
            $model->md5 = $md5;
            $model->sha1 = $sha1;
            $model->image = $filePath;

            if ($model->save()) {
                echo "1";
            }
            else{
                throw new Exception('errors', 1);
                
            }
        } else throw new Exception("Присланы не все параметры", 1);
        
    }

    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }


    public function loadModel($id)
    {
        $model=ParsersIgnoreImages::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }


}
