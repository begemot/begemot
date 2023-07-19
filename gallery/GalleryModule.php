<?php

class GalleryModule extends CWebModule {

    static public $galleryLayout = 'application.views.layouts.galleryLayout';

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'gallery.models.*',
            'gallery.components.*',
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
