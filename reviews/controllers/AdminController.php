<?php

class AdminController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
   public $defaultAction = 'admin';
	public $layout='begemot.views.layouts.column2';
   
   
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}


	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('admin', 'delete', 'approve'),
                'expression'=>'Yii::app()->user->canDo("")'
			),
            array('deny',
                'actions'=>array('admin', 'delete', 'approve'),
                'users'=>array('*')
            ),
		);
	}


   /**
	 * Approves a particular model.
	 * @param integer $id the ID of the model to be approve
	 */
	public function actionApprove($id)
	{
      $result = array('approvedID' => $id);
      if($this->loadModel($id)->setApproved())
         $result['code'] = 'success';
      else 
         $result['code'] = 'fail';
      echo CJSON::encode($result);
	}
   

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}


	public function actionAdmin()
	{
		$model=new Reviews('search');
		$model->unsetAttributes();  
		if(isset($_GET['Reviews']))
			$model->attributes=$_GET['Reviews'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}


	public function loadModel($id)
	{
		$model=Reviews::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='reviews-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
