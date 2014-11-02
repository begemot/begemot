<?php

class SiteController extends Controller
{

	public function actions()
	{
		return array(
			'page'=>array(
				'class'=>'application.modules.pages.components.HTMLAction',
				'layout' => 'postLayout'
			),
		);
	}

	public function actionIndex()
	{
		$model=new Faq;
      $answers = Faq::model()->findAll("published = '1'");
		if(isset($_POST['Faq']))
		{
			$model->attributes=$_POST['Faq'];
			if($model->validate() && $model->save())
			{
				Yii::app()->user->setFlash('contact','Спасибо за ваше письмо.');
				$this->refresh();
			}
		}
		$this->render('index',array('model'=>$model, 'answers'=>$answers));
	}
   
}