<?php

class SiteController extends Controller
{
	public function actionIndex()
	{
      $slides = Slider::model()->findAll();
		$this->render('index', array('slides'=>$slides));
	}
}