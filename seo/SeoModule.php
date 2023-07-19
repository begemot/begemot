<?php

class SeoModule extends CWebModule {
    
   public $layout='begemot.views.layouts.column2';
    
    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'seo.models.*',
            'seo.models.title.*',
            'seo.components.*',
        ));
    }
    public  $controllerMap= array(
                'AXtree'=>array ('class' => 'application.modules.begemot.components.NestedDynaTree.AXcontroller')
            );
    
    public function beforeControllerAction($controller, $action) {


        $component=Yii::createComponent(array(

            'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

        ));
        Yii::app()->setComponent('bootstrap',$component);

            $controller->layout = $this->layout;

        return true;
    }

}
