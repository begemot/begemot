<?php

class RolesImportModule extends CWebModule
{


	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'RolesImport.models.*',
			'RolesImport.components.*',
		));

        Yii::app()->getComponent('bootstrap');
        Yii::app()->theme = 'bootstrap';

        $webroot = Yii::getPathOfAlias('webroot');

        if (!file_exists($webroot.'/files/RolesImport/')){
                //die($webroot.'/files/RolesImport/');
                mkdir($webroot.'/files/RolesImport/',777);
        }

    }

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
            $component=Yii::createComponent(array(

                'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

            ));
            Yii::app()->setComponent('bootstrap',$component);

            $controller->layout = 'begemot.views.layouts.column2';
			return true;
		}
		else
			return false;
	}
}
