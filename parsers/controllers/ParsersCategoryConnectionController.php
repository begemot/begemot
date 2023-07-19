<?php

class ParsersCategoryConnectionController extends Controller
{
	
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
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


	 public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('index', 'view', 'create', 'update', 'createFor', 'admin', 'delete'),
                'expression'=>'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ParsersCategoryConnection;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ParsersCategoryConnection']))
		{
			$model->attributes=$_POST['ParsersCategoryConnection'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionCreateFor($filename, $tab = 'createFor')
	{
		$model=new ParsersCategoryConnection;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ParsersCategoryConnection']))
		{
			$model->attributes=$_POST['ParsersCategoryConnection'];
			if($model->save())
				$this->refresh();
		}

		$fileData = require(Yii::getPathOfAlias('webroot')."/files/parsersData/$filename.data");

		$groups = array();

		if (isset($fileData['groups'])) {
			foreach ($fileData['groups'] as $items) {
				
				foreach ($items as $group) {
					if (!in_array($group, $groups)) {
						$groups[] = $group;
					}
				}
			}
		}

		$data=new ParsersCategoryConnection('search');
		$data->unsetAttributes();  // clear any default values
		if(isset($_GET['ParsersCategoryConnection']))
			$data->attributes=$_GET['ParsersCategoryConnection'];


		$this->render('createFor',array(
			'model'=>$model,
			'groups' => $groups,
			'data' => $data,
			'filename' => $filename,
			'tab' => $tab
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ParsersCategoryConnection']))
		{
			$model->attributes=$_POST['ParsersCategoryConnection'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('ParsersCategoryConnection');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ParsersCategoryConnection('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ParsersCategoryConnection']))
			$model->attributes=$_GET['ParsersCategoryConnection'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return ParsersCategoryConnection the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ParsersCategoryConnection::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param ParsersCategoryConnection $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='parsers-category-connection-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
