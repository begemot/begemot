<?php

class SliderModule extends CWebModule
{

   static public $galleryLayout = 'application.views.layouts.galleryLayout';

   public $defaultController = 'site';

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'slider.models.*',
			'slider.components.*',
		));
	}

   public function beforeControllerAction($controller, $action) {
     
     if ($controller->id != 'site') {

         $component=Yii::createComponent(array(

             'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

         ));
         Yii::app()->setComponent('bootstrap',$component);
     }
     return true;
   }
}
