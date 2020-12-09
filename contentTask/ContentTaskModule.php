<?php

class ContentTaskModule extends CWebModule
{




	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'contentTask.models.*',
			'contentTask.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			$component=Yii::createComponent(array(

				'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

			));
			Yii::app()->setComponent('bootstrap',$component);
			return true;
		}
		else
			return false;
	}
}
