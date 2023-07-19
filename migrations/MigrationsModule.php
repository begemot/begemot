<?php

class MigrationsModule extends CWebModule {

    static public $galleryLayout = 'application.views.layouts.galleryLayout';

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'migrations.components.*',
        	'migrations.database-migrations.*',
            'application.migrations.*',
        	
        ));

    }

    public function beforeControllerAction($controller, $action) {

//        if ($controller->id != 'site') {
//            $bootstrapPath ='/bower_components/bootstrap/dist/js/';
//            Yii::app()->clientScript->registerScriptFile($bootstrapPath.'bootstrap.js');
//        }
        return true;
    }

}

