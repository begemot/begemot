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
				'actions'=>array('index','newMigration'),
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
		$filenamesLocal = array_reverse(CFileHelper::findFiles(Yii::app()->getBasePath() .DIRECTORY_SEPARATOR. "migrations"));

		$filenames = array_merge($filenamesLocal, $filenames);

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
				

				if($results == false){
					$return = $_GET['file'] . " не поддерживает данной функции";
				}
				else{
					$return =  "Выполнено";
				}
			}
			else if(
				file_exists(Yii::app()->getModule('migrations')->getBasePath() . DIRECTORY_SEPARATOR. "database-migrations" .DIRECTORY_SEPARATOR. $_GET['file'] . ".php") 
				OR file_exists(Yii::app()->getBasePath() . DIRECTORY_SEPARATOR. "migrations" .DIRECTORY_SEPARATOR. $_GET['file'] . ".php")
			){
				$model = new $_GET['file'];
				$str = $_GET['go'];

				$results = $model->$str();

				if($results == false){
					$return = "Данное действие уже выполнялось";
				}
				else{
					$return =  "Выполнено";
				}

				
			}
			else $return =  "Файл не был найден";
			
		}

		$this->layout = 'begemot.views.layouts.column1';


		$this->render('admin',array(
			'models'=>$models,
			'return' => $return,
			'time' => time()
		));
                       
	}
	public function actionNewMigration($filename)
	{
		 $migrationsPath = Yii::getPathOfAlias('migrations.database-migrations');

		 $maigrationTemplate = Yii::getPathOfAlias('migrations.components.migrationTemplate').'.php';
		//echo '<pre>';
		 $file = file_get_contents($maigrationTemplate);
        $newClassName = 'm'.date("Ymd_his",time()).'_'.$filename;
        $newFileName = $migrationsPath.'/'.'m'.date("Ymd_his",time()).'_'.$filename.'.php';

        $file = str_replace('<class_name>',$newClassName,$file);

		file_put_contents($newFileName,$file);
		$this->redirect(array('index'));
	}
}