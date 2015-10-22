<?php

class CatalogModule extends CWebModule
{
    const PROMO_CONFIG_FILE_ALIAS = 'webroot.protected.config.catalog.promoImagesConfigFile';
    static public $catalogLayout = 'application.views.layouts.catalogLayout';
    static public $catalogCategoryViewFile = 'rCategoryView';
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
            'reviews.models.*'
        ));

        $this->registerScripts();

    }

    /**
     * Registers the necessary CSS files.
     */
    private function registerScripts()
    {
            $assetsURL=$this->getAssetsURL();
            Yii::app()->clientScript->registerCssFile($assetsURL.'/css/styles.css');
    }

    /**
    * Publishes the module assets path.
    * @return string the base URL that contains all published asset files.
    */
    private function getAssetsURL()
    {
            $assetsPath=Yii::getPathOfAlias('catalog.assets');

            // Republish the assets if debug mode is enabled.

            return Yii::app()->assetManager->publish($assetsPath);
    }

    public function beforeControllerAction($controller, $action)
    {
        if ($controller->id != 'site') {
            $component=Yii::createComponent(array(

                'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

            ));
            Yii::app()->setComponent('bootstrap',$component);
        }

        return true;
    }

    static public function checkEditAccess($authorId = null)
    {
        if (!(Yii::app()->user->canDo('AllContentCatalogEditor') || Yii::app()->user->canDo('OwnContentCatalogEditor'))) {
            throw new CHttpException(403, 'No access.');
        } else {
            if (!Yii::app()->user->canDo('AllContentCatalogEditor') && !is_null($authorId)) {
                if (Yii::app()->user->canDo('OwnContentCatalogEditor')) {
                    if (Yii::app()->user->id !== $authorId) {
                        throw new CHttpException(403, 'No access.');
                    }
                }
            }
        }
    }


}
