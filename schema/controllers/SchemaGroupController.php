<?php
Yii::import('schema.components.ApiFunctions');
class SchemaGroupController extends Controller
{
	public $layout = 'begemot.views.layouts.bs5clearLayout';
	/**
	 * Разрешает запросы только через POST.
	 */
	public function filters()
	{
		return array(
			'accessControl', // Контроль доступа
		);
	}

	/**
	 * Правила доступа к API.
	 */
	public function accessRules()
	{
		return array(
			array(
				'allow',
				'actions' => array('index', 'update'), // Добавили новый метод
				'users' => array('@'), // Только авторизованные пользователи
			),
			array(
				'deny',
				'users' => array('*'),
			),
		);
	}


	public function actionIndex($except = null)
	{
		$this->menu = require dirname(__FILE__) . '/../views/default/commonMenu.php';
		$this->render('index');
	}
	public function actionUpdate($id)
	{
		$this->menu = require dirname(__FILE__) . '/../views/default/commonMenu.php';
		$this->render('update', ['groupId' => $id]);
	}
}
