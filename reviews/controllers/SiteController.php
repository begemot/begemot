<?php

class SiteController extends Controller
{
   public $defaultAction = 'review';

	public function actionReview()
	{
      $model = new Reviews;
		if(isset($_POST['Reviews']))
		{
			$model->attributes=$_POST['Reviews'];
			if($model->validate() && $model->save())
			{
            $this->render('index');
			}
		} else {
         $this->redirect(array('admin'));
      }
	}

}