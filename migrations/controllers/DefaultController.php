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

	public function actionIndex()
	{	
		$return = '';

		$filenames = array_reverse(CFileHelper::findFiles(Yii::app()->getModule('migrations')->getBasePath() .DIRECTORY_SEPARATOR. "database-migrations"));

		$models = array();
		foreach ($filenames as $filename)
		{
		  //remove off the path
		  $explode = explode( DIRECTORY_SEPARATOR, $filename );
		  $file = end( $explode );
		  // remove the extension, strlen('.php') = 4
		  $file = substr( $file, 0, strlen($file) - 4);
		  $models[]= $file;
		}

		if(isset($_GET['file']) && isset($_GET['go'])){

			if($_GET['file'] == "all"){
				$modelsForApply = array_reverse($models);
				foreach ($modelsForApply as $model) {
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
			else if(file_exists(Yii::app()->getModule('migrations')->getBasePath() . DIRECTORY_SEPARATOR. "database-migrations" .DIRECTORY_SEPARATOR. $_GET['file'] . ".php")){
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
			'return' => $return,
			'time' => time()
		));
                       
	}

}