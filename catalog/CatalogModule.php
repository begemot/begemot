<?php

class CatalogModule extends CWebModule
{
    static public $catalogLayout = 'application.views.layouts.catalogLayout';
    static public $catalogCategoryViewLayout = 'application.views.layouts.catalogCategoryViewLayout';
    static public $catalogItemViewLayout = 'application.views.layouts.catalogItemViewLayout';

    public $capcha = false;
    public $tidyleadImage = false;
    public $tidyConfig = array(
        'Three'=>array(

            'imageTag'=>'admin'
        ),
        'One'=>array(

            'imageTag'=>'inner_big'
        )
    );

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'catalog.models.*',
            'catalog.components.*',
        ));


    }

    public function beforeControllerAction($controller, $action)
    {
        if ($controller->id != 'site') {
            Yii::app()->getComponent('bootstrap');
        }

        return true;
    }
}
