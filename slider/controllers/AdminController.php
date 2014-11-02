<?php

class AdminController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
   public $defaultAction = 'admin';
	public $layout='begemot.views.layouts.column2';
   private $imageDir = "files/slider/";
   
   
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
                          'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior'
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
	 * Check if imageDir is exists.
	 */
   private function checkDir()
   {
      if (!file_exists($this->imageDir)) {
         mkdir($this->imageDir, 0777, true);
      }
   }
   
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Slider;

		if(isset($_POST['Slider']))
		{
			$model->attributes=$_POST['Slider'];
         $this->checkDir();
         $model->image=CUploadedFile::getInstance($model,'image');
			if($model->save()){
            $model->image->saveAs($this->imageDir.$model->image);
            $model->image = "/{$this->imageDir}{$model->image}";
            $model->update();
				$this->redirect(array('view','id'=>$model->id));
         }
		}

		$this->render('create',array(
			'model'=>$model,
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
      $prevImage = $model->image;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Slider']))
		{
			$model->attributes=$_POST['Slider'];
         $this->checkDir();
         $model->image=CUploadedFile::getInstance($model,'image');
			if($model->save()){
            if(!empty($model->image)){
               unlink(ltrim($prevImage, "/"));
               $model->image->saveAs($this->imageDir.$model->image);
               $model->image = "/{$this->imageDir}/{$model->image}";
            } else {
               $model->image = $prevImage;
            }
            $model->update();
				$this->redirect(array('view','id'=>$model->id));
         }
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
		$model = $this->loadModel($id);
      unlink(ltrim($model->image, "/"));
      $model->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Slider('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Slider']))
			$model->attributes=$_GET['Slider'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Slider the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Slider::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Slider $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='slider-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
   
   public function actionOrderUp($id){
      $model = $this->loadModel($id);  
      $this->orderUp($id);
   }

   public function actionOrderDown($id){
      $model = $this->loadModel($id);
      $this->orderDown($id);
   }  
}
