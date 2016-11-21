<?php

class PostModule extends CWebModule {

    public $postLayout = 'application.views.layouts.postLayout';
    public $postViewLayout = 'application.views.layouts.postViewLayout';
    public $tidyleadImage = false;
    public $limit = 1;
    public $tidyConfig = array(
        'Three'=>array(

        'imageTag'=>'admin'
        ),
        'One'=>array(

        'imageTag'=>'inner_big'
        )
    );

    //static public $catalogCategoryViewLayout = 'application.views.layouts.catalogCategoryViewLayout';
    //static public $catalogItemViewLayout = 'application.views.layouts.catalogItemViewLayout';

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'post.models.*',
            'post.components.*',
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
