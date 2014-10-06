<?php

class FaqModule extends CWebModule
{

   static public $galleryLayout = 'application.views.layouts.galleryLayout';

   public $defaultController = 'site';
   
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'faq.models.*',
			'faq.components.*',
		));
	}

   public function beforeControllerAction($controller, $action) {
     
     if ($controller->id != 'site') {

        Yii::app()->getComponent('bootstrap');
     }
     return true;
   }
   
   
}
