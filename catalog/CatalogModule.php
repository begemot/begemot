<?php

class CatalogModule extends CWebModule
{
    const PROMO_CONFIG_FILE_ALIAS = 'webroot.protected.config.catalog.promoImagesConfigFile';
    const CAT_ITEM_CONFIG_FILE_ALIAS = 'webroot.protected.config.catalog.categoryItemPictureSettings';
    const CAT_CONFIG_FILE_ALIAS = 'webroot.protected.config.catalog.categoryPictureSettings';
    static public $catalogLayout = 'application.views.layouts.catalogLayout';
    static public $catalogCategoryViewFile = 'rCategoryView';
    static public $catalogCategoryViewLayout = 'application.views.layouts.catalogCategoryViewLayout';
    static public $catalogItemViewLayout = 'application.views.layouts.catalogItemViewLayout';


    public $pagination = false;
    public $baseLayout = null;
    public $itemLayout = null;
    public $capcha = false;

    public $tidyleadImage = false;
    public $tidyConfig = array(
        'Three' => array(

            'imageTag' => 'admin'
        ),
        'One' => array(

            'imageTag' => 'inner_big'
        )
    );

    public function init()
    {

        CatalogModule::$catalogLayout = $this->baseLayout;
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'catalog.models.*',
            'catalog.components.*',
        ));





        $this->registerScripts();
    }

    /**
     * Registers the necessary CSS files.
     */
    private function registerScripts()
    {
        $assetsURL = $this->getAssetsURL();
        Yii::app()->clientScript->registerCssFile($assetsURL . '/css/styles.css');
    }

    /**
     * Publishes the module assets path.
     * @return string the base URL that contains all published asset files.
     */
    private function getAssetsURL()
    {
        $assetsPath = Yii::getPathOfAlias('catalog.assets');

        // Republish the assets if debug mode is enabled.

        return Yii::app()->assetManager->publish($assetsPath);
    }
    public function beforeControllerAction($controller, $action)
    {
        // Массив исключений
        $exclusions = [
            'site' => ['*'], // Все действия контроллера 'site'
            'catalogAndSchema' => ['*'], // Все действия контроллера 'catalogAndSchema'
            'catCategory' => ['massItemsMoveBetweenCategories'], // Определенные действия другого контроллера
            'catItemOptions' => ['*'], // Определенные действия другого контроллера
            'catItem' => ['massImages', 'video', 'videoToItem', 'modifToItem'],
            'mass' => ['titleAlt']
        ];

        // Проверка исключений
        $controllerId = $controller->id;
        $actionId = $action->id;


        $component = Yii::createComponent([
            'class' => 'application.modules.catalog.components.CategoryManager'
        ]);
        Yii::app()->setComponent('categoryManager', $component);
        if (isset($exclusions[$controllerId])) {
            if (in_array('*', $exclusions[$controllerId]) || in_array($actionId, $exclusions[$controllerId])) {
                return true; // Исключение, не подключаем Bootstrap
            }
        }

        // Подключение Bootstrap компонента
        $component = Yii::createComponent([
            'class' => 'begemot.extensions.bootstrap.components.Bootstrap'
        ]);
        Yii::app()->setComponent('bootstrap', $component);

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

    static public function getMenu()
    {
        return require dirname(__FILE__) . '/views/catItem/commonMenu.php';
    }
}