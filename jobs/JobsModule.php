<?php

class JobsModule extends CWebModule {

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components

        foreach(glob(Yii::app()->basePath . "/modules/*", GLOB_ONLYDIR) as $path) {    

            if(file_exists($path)){
                if(file_exists($path . "/jobs")){
                    Yii::import('application.modules.' . basename($path) . ".jobs.*");
                }
            }
            
        } 

        $this->setImport(array(
        	'jobs.components.*',
            'pictureBox.components.*',
            'parsers.components.*',
            'application.jobs.*',
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
