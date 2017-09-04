<?php

class SiteController extends Controller
{
public $layout = '//layouts/postLayout';


	public function actionIndex()
	{
		$model=new Faq;
      $answers = Faq::model()->findAll("published = '1'");
		if(isset($_POST['Faq']))
		{
			$model->attributes=$_POST['Faq'];
			if($model->validate() && $model->save())
			{
				Yii::app()->user->setFlash('faqSuccess',"Ваш вопрос отправлен!");
				$this->refresh();
			}
		}
		$this->render('index',array('model'=>$model, 'answers'=>$answers));
	}
   
}