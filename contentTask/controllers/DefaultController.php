<?php

class DefaultController extends Controller
{
	public $layout='begemot.views.layouts.column2';

	public function actionIndex()
	{

		$this->render('index');
	}

}