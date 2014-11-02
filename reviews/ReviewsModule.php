<?php

class ReviewsModule extends CWebModule
{
   
   const APPROVE_ACTION_ROUTE = "reviews/admin/approve";

   public $defaultController = 'site';
   
	public function init()
	{
		$this->setImport(array(
			'reviews.models.*',
			'reviews.components.*',
			'catalog.models.*',
		));
	}

    public function beforeControllerAction($controller, $action) {
        
        if ($controller->id != 'site') {

           Yii::app()->getComponent('bootstrap');
        }
        return true;
    }
}
