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
				'actions'=>array('index','logView', 'turnOnOff', 'changeTime', 'setTask', 'jobs', 'removeTask', 'runJob','manualRunJob'),
                'expression'=>'Yii::app()->user->canDo("")'
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSetTask()
	{
		if (isset($_POST['filename'])) {

			unset($_POST['listname']);
			unset($_POST['createNew']);

			$JobManager = new JobManager;

            $logMessage = 'Контроллер actionSetTask '.var_export($_POST,true);
            Yii::log($logMessage,'trace','cron');


			$JobManager->newTask($_POST);
			
		} else{
            $logMessage = 'Ошибка! Задача не создалась.';
            Yii::log($logMessage,'trace','cron');
			throw new Exception("Error Processing Request", 1);
			
		}
	}
	public function actionManualRunJob()
	{
        $jobManager = new JobManager();

        $all = $jobManager->getListCronJob();
        $jobName = $_POST['name'];
		$item = $all[$jobName];

        $className = $item['filename'];
        $classItem = new $className;


        $logMessage = 'JobManager ManualRunJob - запускаем в ручном режиме '.$jobName;
        Yii::log($logMessage,'trace','cron');
        $classItem->runJob($item);

        $tmpJobList = $jobManager->getListCronJob();
        if (isset($tmpJobList[$jobName])){
            $tmpJobList[$jobName]['lastExecuted'] = time();
            $jobManager->saveConfigFile($tmpJobList);
        }

        //echo $_POST['name'];
       // print_r($item);



//		if (isset($_POST['name'])) {
//
//			$params = null;
//			if (isset($_POST['type'])){
//				$params =['type'=>$_POST['type']];
//			}
//			$class = new $_POST['name'];
//			if(!$class->runJob($params))  throw new CHttpException(400,'Ошибка');
//
//		} else{
//			throw new CHttpException(400,'Ошибка');
//		}
	}
    public function actionRunJob()
    {


        if (isset($_POST['name'])) {

			$params = null;
			if (isset($_POST['type'])){
				$params =['type'=>$_POST['type']];
			}
            $class = new $_POST['name'];
            if(!$class->runJob($params))  throw new CHttpException(400,'Ошибка');

        } else{
            throw new CHttpException(400,'Ошибка');
        }
    }

	public function actionIndex()
	{
		$crons = new JobManager();

		$this->render('index',array(
		 	'itemList' => $crons->getListCronJob(),
		 ));

	}

	public function actionJobs()
	{
		$jobManager = new JobManager();

		$this->render('jobs',array(
		 	'itemList' => $jobManager->getAllJobs(),
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
        } else{
            throw new CHttpException(400,'Ошибка');
        }
    }

    public function actionRemoveTask()
    {
        if (isset($_POST['name'])) {
            $JobManager = new JobManager;
            $JobManager->removeTask($_POST['name']);
        } else{
            throw new CHttpException(400,'Ошибка');
        }
    }

	public function actionChangeTime()
	{
		if (isset($_POST['name'])) {

			unset($_POST['item']);
			unset($_POST['changeTime']);

			$JobManager = new JobManager;
			
			echo $JobManager->changeTime($_POST);

			
		} else{
			throw new Exception("Error Processing Request", 1);
			
		}
	}
    public function actionLogView()
    {
        $this->render('logView');
    }

}
