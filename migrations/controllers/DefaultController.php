<?php

class DefaultController extends Controller
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
        
        public function behaviors(){
                return array(
                        'CBOrderControllerBehavior' => array(
                                'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
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
				'actions'=>array('admin','delete','orderUp','orderDown','manageGallery','create','update','index','view'),
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

	public function actionManageGallery($id) 
	{
		$this->render('manageGallery',array(
			'model'=>$this->loadModel($id),
		));
	}
        
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Gallery;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Gallery']))
		{
			$model->attributes=$_POST['Gallery'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Gallery']))
		{
			$model->attributes=$_POST['Gallery'];
			$model->save();
			//	$this->redirect(array('view','id'=>$model->id));
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
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{	
		$return = '';

		$filenames =CFileHelper::findFiles(Yii::app()->getModule('migrations')->getBasePath() . "/database-migrations");

		$models = array();
		foreach ($filenames as $filename)
		{
		  //remove off the path
		  $file = end( explode( '/', $filename ) );
		  // remove the extension, strlen('.php') = 4
		  $file = substr( $file, 0, strlen($file) - 4);
		  $models[]= $file;
		}

		if(isset($_GET['file']) && isset($_GET['go'])){

			if($_GET['file'] == "all"){
				foreach ($models as $model) {
					$model = new $model;

					$results = $model->$_GET['go']();
				}
				

				if($results == false && $_GET['file'] != "all"){
					$return = $_GET['file'] . " не поддерживает данной функции";
				}
				else{
					$return =  "Выполнено";
				}
			}
			else if(file_exists(Yii::app()->getModule('migrations')->getBasePath() . "/database-migrations/" . $_GET['file'] . ".php")){
				$model = new $_GET['file'];

				$results = $model->$_GET['go']();

				if($results == false){
					$return = $_GET['file'] . " не поддерживает данной функции";
				}
				else{
					$return =  "Выполнено";
				}

				
			}
			else $return =  "Файл не был найден";
			
		}

		


		$this->render('admin',array(
			'models'=>$models,
			'return' => $return
		));
                       
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Gallery('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Gallery']))
			$model->attributes=$_GET['Gallery'];

		$this->render('admin',array(
			'model'=>$model,
		));
                             
                
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Gallery::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='gallery-form')
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