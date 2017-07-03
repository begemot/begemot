<?php

class TasksController extends GxController {
public $layout='begemot.views.layouts.column2';

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

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('index', 'view', 'create', 'update','admin', 'delete'),
                'expression'=>'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Tasks'),
		));
	}

	public function actionCreate() {
		$model = new Tasks;

		$this->performAjaxValidation($model, 'tasks-form');

		if (isset($_POST['Tasks'])) {
			$model->setAttributes($_POST['Tasks']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Tasks');

		$this->performAjaxValidation($model, 'tasks-form');

		if (isset($_POST['Tasks'])) {
			$model->setAttributes($_POST['Tasks']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
		));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'Tasks')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Tasks');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Tasks('search');
		$model->unsetAttributes();

		if (isset($_GET['Tasks']))
			$model->setAttributes($_GET['Tasks']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}