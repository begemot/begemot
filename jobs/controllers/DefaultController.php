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
				'actions'=>array('index', 'turnOnOff', 'changeTime', 'setTask', 'jobs', 'removeTask', 'runJob'),
                'expression'=>'Yii::app()->user->canDo("")'
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSetTask()
	{
		if (isset($_POST['time']) AND isset($_POST['hour'])) {

			$JobManager = new JobManager;
			
			echo $JobManager->newTask($_POST);

			
		}
	}

	public function actionRunJob()
	{
		if (isset($_POST['name'])) {

			$class = new $_POST['name'];
			if($class->runJob()) echo "1";


			
		}

		print_r($_POST);
	}

	public function actionIndex()
	{
		$crons = new JobManager();

		$this->render('index',array(
		 	'itemList' => (array) $crons->getListCronJob(),
		 ));

	}

	public function actionJobs()
	{

		$jobManager = new JobManager();

		$this->render('jobs',array(
		 	'itemList' => (array) $jobManager->getAllJobs(),
		 	'jobManager' => $jobManager
		 ));
	}

	public function actionTurnOnOff()
	{
		if (isset($_POST['name']) AND isset($_POST['turn'])) {

			$JobManager = new JobManager;
			if ($_POST['turn'] == 1) {
				$JobManager->turnOn($_POST['name']);
			} else{
				$JobManager->turnOff($_POST['name']);
			}

			echo "1";
		}


	}

	public function actionRemoveTask()
	{
		if (isset($_POST['name'])) {

			$JobManager = new JobManager;

			$JobManager->removeTask($_POST['name']);

			echo "1";
		}

	}

	public function actionChangeTime()
	{
		if (isset($_POST['time']) AND isset($_POST['name'])) {

			$JobManager = new JobManager;
			
			echo $JobManager->changeTime($_POST['name'], (int) $_POST['time'], (int) $_POST['hour']);

			
		}
	}


}