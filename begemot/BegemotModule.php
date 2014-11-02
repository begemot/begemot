<?php

class BegemotModule extends CWebModule
{
        static public function fullDelDir($directory){
                $dir = opendir($directory); 
                while($file = readdir($dir)) { 
                  if (is_file($directory."/".$file)) { 
                    unlink($directory."/".$file);
           
                  } elseif (is_dir($directory."/".$file) && $file !== "." && $file !=="..") { 
                    self::fullDelDir($directory."/".$file); 
                  } 
                } 
                closedir($dir); 
                rmdir($directory); 
           
                
        }
        
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'begemot.models.*',
			'begemot.components.*',
		));
                 Yii::app()->getComponent('bootstrap');
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
