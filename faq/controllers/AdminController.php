<?php

class AdminController extends Controller
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
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

  public function behaviors(){
          return array(
                  'CBOrderControllerBehavior' => array(
                          'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
                          'groupName' => 'cid'
                  )
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
				'actions'=>array('admin','delete','index','view','create','update','orderUp','orderDown'),
            'expression' => 'Yii::app()->user->canDo("")'
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
		$model=new Faq;
      $cats = FaqCats::model()->findAll();
		if(isset($_POST['Faq']))
		{
			$model->attributes=$_POST['Faq'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
         'cats' => $cats,
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
      $cats = FaqCats::model()->findAll();
		if(isset($_POST['Faq']))
		{
			$model->attributes=$_POST['Faq'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
         'cats' => $cats,
		));
	}


	public function actionDelete($id)
	{
      $result = array('deletedID' => $id);
      if($this->loadModel($id)->delete())
         $result['code'] = 'success';
      else 
         $result['code'] = 'fail';
      echo CJSON::encode($result);
	}

	public function actionIndex($cid = 0)
	{
		$model=new Faq('search');
		$model->unsetAttributes();  
		if(isset($_GET['Faq']))
			$model->attributes=$_GET['Faq'];

		$this->render('index',array(
			'model'=>$model,
         'cid' => $cid,
		));
	}	



	public function loadModel($id)
	{
		$model=Faq::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='faq-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
   
   public function actionOrderUp($id){
      $model = $this->loadModel($id);  
      $this->groupId = $model->cid;
      $this->orderUp($id);
   }

   public function actionOrderDown($id){
      $model = $this->loadModel($id);
      $this->groupId = $model->cid;
      $this->orderDown($id);
   }  
   
}
