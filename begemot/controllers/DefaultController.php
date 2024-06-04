<?php

class DefaultController extends Controller
{
	public $layout = 'begemot.views.layouts.bs5clearLayout';

	public function actions()
	{
		return array(

			'fileManager' => array(
				'class' => 'begemot.extensions.elfinder.ElFinderAction',
			),
		);
	}


	public function actionIndex()
	{

		if (isset(Yii::app()->user) && Yii::app()->user->isGuest) {
			$this->redirect('/user/login');
		} else {
			$this->render('index');
		}
	}

	public function actionLogin()
	{
		$model = new BegemotLoginForm;

		// if it is ajax validation request
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if (isset($_POST['BegemotLoginForm'])) {
			$model->attributes = $_POST['BegemotLoginForm'];
			// validate user input and redirect to the previous page if valid
			if ($model->validate() && $model->login())
				$this->redirect('/admin');
		}
		// display the login form
		$this->render('index', array('model' => $model));
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect('/begemot');
	}
}