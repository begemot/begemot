<?php

class SiteController extends GxController 
{
	public $layout = '//layouts/main';

	public function filters() {
		return array(
			'accessControl', 
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
            array('deny',
                'actions'=>array('update',  'create', 'delete'),
                'expression'=>'Yii::app()->user->isGuest'
            ),
		);
    }

    protected function beforeAction($action){
		if(Yii::app()->request->isAjaxRequest){
	        Yii::app()->clientScript->scriptMap['jquery.js'] = false;
	        Yii::app()->clientScript->scriptMap['*.css'] = false;
		}

		return true;
	}

    public function actionDefault(){


  //   	$this->render('view', array(
		// 	'model' => ,
		// ));
    }

	public function actionView($id) {

		$payments = PaymentArchive::model()->findAllByAttributes(array('task_id' => $id));
		$this->render('view', array(
			'model' => Tasks::model()->findByPk($id),
			'payments' => $payments
		));
	}

	public function actionAll($sort = 'id', $order = 'a'){
		$sortReturn = $sort;

		$orderVar = $order;

		if($order == "a"){
			$order = "ASC";
		} else $order = "DESC";

		

		if($sort == 'popular'){
			$sort = "likes " . $order . ", comments";
		} 
		else if($sort == "date"){
			$sort = 'create_time';
		}

		$newTasks = Tasks::model()->findAll(array('order' =>  $sort . ' ' . $order . ', create_time DESC', 'limit' => 7));
		$hotTasks = Tasks::model()->findAll(array('order' => 'create_time DESC, likes DESC, comments DESC', 'limit' => 7));
		$doingTasks = Tasks::model()->findAll(array('condition' => 'done = 0', 'order' => $sort . ' ' . $order, 'limit' => 7));
		$doneTasks = Tasks::model()->findAll(array('condition' => 'done = 1', 'order' => $sort . ' ' . $order, 'limit' => 7));

		$tasks = array(
			'newTasks' => array('data' => $newTasks, 'id' => 'new', 'title' => 'Свежие задания', 'title_t' => 'Svesqe_sadanija'),
			'hotTasks' => array('data' => $hotTasks, 'id' => 'hot', 'title' => 'Горячие задания', 'title_t' => 'Gorjachie_zadanija'),
			'doingTasks' => array('data' => $doingTasks, 'id' => 'delajutsa', 'title' => 'Задания на выполнении', 'title_t' => 'Zadanija_na_vypolnenii'),
			'doneTasks' => array('data' => $doneTasks, 'id' => 'sdelannqe', 'title' => 'Выполненные задания', 'title_t' => 'Vypolnennye_zadanija'),
		);

		$this->render('all', array(
			'tasks' => $tasks,
			'sort' => $sortReturn,
			'order' => $orderVar
		));
	}

	public function actionCategory($id, $sort = 'id', $order = 'a'){
		$sortReturn = $sort;

		$orderVar = $order;

		if($order == "a"){
			$order = "ASC";
		} else $order = "DESC";

		if($sort == 'popular'){
			$sort = "likes " . $order . ", comments";
		} 
		else if($sort == "date"){
			$sort = 'create_time';
		}

		if($id == 'delajutsa'){
			$tasks = Tasks::model()->findAll(array('condition' => 'done = 0', 'order' => $sort . ' ' . $order));
			$title = 'Задания на выполнении';
			$title_t = 'Zadanija_na_vypolnenii';
		}
		else if($id == 'kassovqe'){
			$tasks = Tasks::model()->findAll(array('condition' => 'price > 0', 'order' => $sort . ' ' . $order));
			$title = 'Кассовые задания';
			$title_t = 'Kassovye_zadanija';
		}
		else if($id == 'sdelannqe'){
			$tasks = Tasks::model()->findAll(array('condition' => 'done = 1', 'order' => $sort . ' ' . $order));
			$title = 'Выполненные задания';
			$title_t = 'Vypolnennye_zadanija';
		}
		else if($id == 'hot'){
			$tasks = Tasks::model()->findAll(array('order' => 'likes DESC, comments DESC, create_time DESC'));
			$title = 'Горячие задания';
			$title_t = 'Gorjachie_zadanija';
		}
		else{
			$tasks = Tasks::model()->findAll(array('order' => $sort . ' ' . $order . ', create_time DESC'));
			$title = 'Свежие задания';
			$title_t = 'Svesqe_sadanija';
		}

		$this->render('category', array(
			'tasks' => $tasks,
			'id' => $id,
			'title' => $title,
			'sort' => $sortReturn,
			'order' => $orderVar
		));
	}

	public function actionLikeOrDislike($id, $doLikeOrDislike){

		$task=Tasks::model()->findByPk($id);
		$needToDowngrade = false;


    	if(Yii::app()->user->id != null && count(TasksLikesAndDislikes::model()->findByAttributes(array(
    			'user_id' => Yii::app()->user->id, 
    			'task_id' => $id, 
    			'like_or_dislike' => intval($doLikeOrDislike)
    	))) === 0){


    	
        	if(TasksLikesAndDislikes::model()->deleteAllByAttributes(array('user_id' => Yii::app()->user->id, 'task_id' => $id))){
        		$needToDowngrade = true;
        	}
            
            $addNewOne = new TasksLikesAndDislikes(); 
            $addNewOne->user_id = Yii::app()->user->id;
            $addNewOne->task_id = $id;
            $addNewOne->like_or_dislike = intval($doLikeOrDislike);
            if($addNewOne->save()){

            	
				$transaction=$task->dbConnection->beginTransaction();
				try
				{
				    // поиск и сохранение — шаги, между которыми могут быть выполнены другие запросы,
				    // поэтому мы используем транзакцию, чтобы удостовериться в целостности данных
				    
				    if(intval($doLikeOrDislike)){
				    	$task->likes++;
				    	if($needToDowngrade) $task->dislikes--;
				    } else {
				    	$task->dislikes++;
				    	if($needToDowngrade) $task->likes--;
				    }

				    if($task->likes < 0) $task->likes = 0;
                    if($task->dislikes < 0) $task->dislikes = 0;
				    
				    if($task->save())
				        $transaction->commit();
				    else
				        $transaction->rollback();
				}
				catch(Exception $e)
				{
				    $transaction->rollback();
				    Yii::log($e, 3, 'transaction_error');
				}
            }

        }

        echo json_encode(array('success' => true, 'likes' => $task->likes, 'dislikes' => $task->dislikes));

        Yii::app()->end();
    }

    public function actionTaskToUserLike($id){

        if (Yii::app()->user->id != null) {

            $addNewOne = new TasksToUserLikes(); 
            $addNewOne->user_id = Yii::app()->user->id;
            $addNewOne->tasks_to_user_id = $id;
          
            if($addNewOne->save()){

            	$model=TasksToUser::model();
				$transaction=$model->dbConnection->beginTransaction();
				try
				{
				    // поиск и сохранение — шаги, между которыми могут быть выполнены другие запросы,
				    // поэтому мы используем транзакцию, чтобы удостовериться в целостности данных
				    $model=$model->findByPk($id);
				    $model->likes = $model->likes + 1;

				    if($model->save()){
				    	$transaction->commit();
				    }
				    else{
				    	var_dump($model->getErrors());
				    	$transaction->rollback();
				    }
				        
				}
				catch(Exception $e)
				{
				    $transaction->rollback();
				    Yii::log($e, 3, 'transaction_error');
				}
            }

        }

        echo json_encode(array('success' => true, 'likes' => $model->likes));

        Yii::app()->end();
    }

    public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			$model = $this->loadModel($id, 'Tasks');

			$array = Yii::app()->authManager->getRoles(Yii::app()->user->id);
	        reset($array);
	        $first_key = key($array);
			if($model->user_id == Yii::app()->user->id || $first_key == 'admin joystarter'){
				$model->delete();
				echo json_encode(array('success' => true));
				Yii::app()->end();
			}
			else{
				echo json_encode(array('error' => 'Данное задание не принадлежит Вам'));
				Yii::app()->end();
			}
				

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}


	public function actionCreate(){

		$this->layout = '//layouts/modal';
		$model = new Tasks;

		if(isset($_POST['ajax'])){
			echo UActiveForm::validate(array($model));
			Yii::app()->end();
		}

		if (isset($_POST['Tasks'])) {
			$model->setAttributes($_POST['Tasks']);
			if($model->validate()){
			

				if ($model->save()) {

					if (Yii::app()->getRequest()->isAjaxRequest){
						$json['redirect'] = Yii::app()->createUrl("/tasks/site/view", array('itemName' => $model->title_t, 'id' => $model->id, 'catId' => 'new'));
						$json['success'] = true;
						echo json_encode($json);
						Yii::app()->end();
					}
					else
						$this->redirect(array("/tasks/site/view", array('itemName' => $model->title_t, 'id' => $model->id, 'catId' => 'new')));
				}
			}
			else{
				if(isset($_POST['ajax'])){
					echo $model->getErrors();
					Yii::app()->end();
				}
			}
		}
		else{
			if(Yii::app()->request->isAjaxRequest){
				$this->renderPartial('create',array('model'=>$model), false, true);

			} else{
				$this->render('create', array( 'model' => $model));
			}
		}

	}

	public function actionUpdate($id){
		$this->layout = '//layouts/modal';
		$model = $this->loadModel($id, 'Tasks');


		if (Yii::app()->user->isGuest) {
			throw new Exception("Ошибка запроса", 1);
		}
		if(!Yii::app()->user->isAdmin() && $model->user_id != Yii::app()->user->id){
			throw new Exception("Ошибка запроса", 1);
		}

		if(isset($_POST['ajax'])){
			echo UActiveForm::validate(array($model));
			Yii::app()->end();
		}

		if (isset($_POST['Tasks'])) {
			$model->setAttributes($_POST['Tasks']);
			if($model->validate()){
			

				if ($model->save()) {

					if (Yii::app()->getRequest()->isAjaxRequest){
						$json['redirect'] = Yii::app()->createUrl("/tasks/site/view", array('itemName' => $model->title_t, 'id' => $model->id, 'catId' => 'new'));
						$json['success'] = true;
						echo json_encode($json);
						Yii::app()->end();
					}
					else
						$this->redirect(array("/tasks/site/view", array('itemName' => $model->title_t, 'id' => $model->id, 'catId' => 'new')));
				}
			}
			else{
				if(isset($_POST['ajax'])){
					echo $model->getErrors();
					Yii::app()->end();
				}
			}
		}
		else{
			$d = 0;
			if($model->getItemMainPicture('main', false, true) != ""){
				$model->image = str_replace('_main', '', $model->getItemMainPicture('main', false, true));

				$image = new Imagick(dirname(Yii::app()->basePath) . $model->image);
				$d = $image->getImageGeometry(); 
			}

			if(Yii::app()->request->isAjaxRequest){
				$this->renderPartial('update',array('model'=>$model, 'image_width' => $d['width']), false, true);

			} else{
				$this->render('update', array( 'model' => $model, 'image_width' => $d['width']));
			}
		}

	}

	public function actionWillDo($task_id){
		$model = new TasksToUser;

		if(isset($_POST['ajax'])){
			echo UActiveForm::validate(array($model));
			Yii::app()->end();
		}

		if (isset($_POST['TasksToUser'])) {
			$model->setAttributes($_POST['TasksToUser']);
			if($model->validate()){
			

				if ($model->save()) {

					if (Yii::app()->getRequest()->isAjaxRequest){
						echo json_encode(array('reload' => true, 'success' => true));
						Yii::app()->end();
					}
					else
						$this->redirect(array('view', 'id' => $model->id));
				}
			}
			else{
				if(isset($_POST['ajax'])){
					echo $model->getErrors();
					Yii::app()->end();
				}
			}
		}
		else{
			if(Yii::app()->request->isAjaxRequest){
				$this->renderPartial('willDo',array('model'=>$model), false, true);

			} else{
				$this->layout = 'modal';
				$this->render('willDo', array( 'model' => $model));
			}
		}

	}

	public function actionSubscribe($task_id){
		$model = new TasksSubscribe;

		if(isset($_POST['ajax'])){
			echo UActiveForm::validate(array($model));
			Yii::app()->end();
		}

		if (isset($_POST['TasksSubscribe'])) {
			$model->setAttributes($_POST['TasksSubscribe']);
			if($model->validate()){
			

				if ($model->save()) {

					if (Yii::app()->getRequest()->isAjaxRequest){
						echo json_encode(array('success' => true, 'modalMessage' => 'Теперь вы будете в курсе всех событий JoyStarter.'));
						Yii::app()->end();
					}
					else
						$this->redirect(array('view', 'id' => $model->id));
				}
			}
			else{
				if(isset($_POST['ajax'])){
					echo $model->getErrors();
					Yii::app()->end();
				}
			}
		}
		else{
			if(Yii::app()->request->isAjaxRequest){
				$this->renderPartial('subscribe',array('model'=>$model), false, true);

			} else{
				$this->layout = 'modal';
				$this->render('subscribe', array( 'model' => $model));
			}
		}

	}


}