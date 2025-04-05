<?php

class SiteController extends Controller
{

    //    public function filters() {
    //        return array(
    //            array(
    //                'COutputCache + index',
    //                'duration' => 60,
    //             //   'varyByParam' => array('id'),
    //            ),
    //        );
    //    }
    public $layout = 'clearNoAnimate';


    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'begemot.extensions.capcha.CaptchaExtendedAction',
                'mode' => CaptchaExtendedAction::MODE_MATH,
            ),
        );
    }

    public function init()
    {

        // import class paths for captcha extended
        Yii::$classMap = array_merge(Yii::$classMap, array(
            'CaptchaExtendedAction' => Yii::getPathOfAlias('begemot.extensions.capcha') . DIRECTORY_SEPARATOR . 'CaptchaExtendedAction.php',
            'CaptchaExtendedValidator' => Yii::getPathOfAlias('begemot.extensions.capcha') . DIRECTORY_SEPARATOR . 'CaptchaExtendedValidator.php'
        ));
    }

    public function actionIndex()
    {

        $this->layout = $this->module->baseLayout;


        $categories = CatCategory::model()->findAll(array('condition' => 'level = 0', 'order' => '`order`'));


        $this->render('index', ['categories' => $categories]);
    }


    public function actionGetField($itemId, $field)
    {
        $item = CatItem::model()->findByPk($itemId);

        $returnVal = $item->$field;

        if ($field == 'price') {
            $returnVal = number_format($returnVal, 0, ',', ' ');
        }
        echo $returnVal;
    }


    public function actionItemView($item = 0, $catId = 0)
    {

        if (!is_null($this->module->itemLayout)) {
            $this->layout = $this->module->itemLayout;
        } else {
            $this->layout = $this->module->baseLayout;
        }

        $uri = $_SERVER['REQUEST_URI'];

        $item = CatItem::model()->findByPk($item);

        $this->pageTitle = $item->seo_title;
        //        $this->layout = CatalogModule::$catalogItemViewLayout;
        $category = CatCategory::model()->findByPk($item->catId);
        if ($category) {
            if ($category->layout) {
                $this->layout = $category->layout;
            }

            $hrefParams = array(
                'title' => $category->name_t,
                'catId' => $category->id,
                'itemName' => $item->name_t,
                'item' => $item->id,
            );
            if ($category->itemViewFile) {

                $itemViewFile = $category->itemViewFile;
            } else {
                $itemViewFile = 'itemView';
            }
        } else {
            $hrefParams = array(
                'title' => 'none',
                'catId' => 1,
                'itemName' => $item->name_t,
                'item' => $item->id,
            );
            $itemViewFile = 'itemView';
        }


        $itemHref = Yii::app()->urlManager->createUrl('catalog/site/itemView', $hrefParams);

        if ($itemHref !== $uri) {
            $this->redirect($itemHref, true, 301);
        }

        $modifItems = $item->modifications;

        $this->render($itemViewFile, array('item' => $item, 'category' => $category, 'modifItem' => $modifItems));
    }


    public function actionCategoryView($catId = 0)
    {

        $category = CatCategory::model()->findByPk($catId);

        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        if ($category->layout) {
            $this->layout = $category->layout;
        }

        $maximalPriceValue = CatItem::model()->getItemWithMaximalPrice($catId);
        $criteria = new CDbCriteria;

        $criteria->select = 't.itemId';
        $criteria->condition = '`t`.`catId` = ' . $catId . '';
        $criteria->with = array(
            'item' => array(
                'condition' => 'published=1'
            )
        );
        $criteria->order = 'item.top DESC, t.order ASC';

        if (isset($_GET['sort'])) {
            $sort = ($_GET['sort'] == 'asc') ? 'asc' : 'desc';
            $criteria->order = 'item.price ' . $sort;
        }

        if (isset($_GET['priceMin']) && isset($_GET['priceMax'])) {
            $priceMin = (int)$_GET['priceMin'];
            $priceMax = (int)$_GET['priceMax'];

            $criteria->addBetweenCondition('price', $priceMin, $priceMax);
        }
        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => $criteria, 'pagination' => array('pageSize' => 1000)));

        // $dataProvider=CatItemsToCat::model()->published()->with('item')->findAll();top

        $this->render('categoryView', array('categoryItems' => $dataProvider->getData(), 'category' => $category, 'maximalPriceValue' => $maximalPriceValue));


        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => array('select' => 't.itemId', 'condition' => '`t`.`catId` = ' . $catId . '', 'with' => array('item' => array('condition' => 'published=1')), 'order' => 't.order'), 'pagination' => array('pageSize' => 1000)));
        // $dataProvider=CatItemsToCat::model()->published()->with('item')->findAll();
        $this->render('categoryView', array('categoryItems' => $dataProvider->getData(), 'category' => $category));
    }


    public function actionRCategoryView($catId = 0, $page = 0)
    {


        //        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        $this->layout = $this->module->baseLayout;

        $category = CatCategory::model()->findByPk($catId);

        $this->pageTitle = $category->seo_title;
        $maximalPriceValue = CatItem::model()->getItemWithMaximalPrice($catId);
        $parentCategory = null;

        if ($category && $category->pid != "-1") {

            $parentCategory = CatCategory::model()->findByPk($category->pid);
        }

        if ($category->layout) {
            $this->layout = $category->layout;
        }
        $catsIDs = $category->getAllCatChilds($catId);


        $iDsArray = array($catId);
        foreach ($catsIDs as $catData) {
            $iDsArray[] = $catData['id'];
        }

        $iDsStr = '(' . implode(',', $iDsArray) . ')';

        $criteria = new CDbCriteria;

        $criteria->select = 't.itemId, t.catId';
        $criteria->condition = '`t`.`catId` = ' . $catId . '';

        $criteria->with = array(
            'item' => array(
                'condition' => 'published=1'
            )

        );

        // $criteria->group = 'item.id';

        $criteria->distinct = true;
        $criteria->order = 't.order ';

        if (isset($_GET['sort'])) {
            $sort = ($_GET['sort'] == 'asc') ? 'asc' : 'desc';
            $criteria->order = 'item.price ' . $sort;
        }


        if (isset($_GET['sortByCustomField'])) {
            $sort = ($_GET['sort'] == 'asc') ? 'asc' : 'desc';
            $criteria->order = 'item.' . $_GET['sortByCustomField'] . ' ' . $sort;
        }

        if (isset($_GET['priceMin']) && isset($_GET['priceMax'])) {
            $priceMin = (int)$_GET['priceMin'];
            $priceMax = (int)$_GET['priceMax'];

            $criteria->addBetweenCondition('price', $priceMin, $priceMax);
        }


        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => array('select' => 't.itemId', 'condition' => '`t`.`catId` in ' . $iDsStr . '', 'with' => 'item', 'order' => '`item`.`top` desc,`item`.`price`', 'distinct' => true, 'group' => '`t`.`itemId`')));

        if ($this->module->pagination) {


            $pagination = array('pageSize' => $this->module->perPage, 'currentPage' => $page);
        } else {
            $pagination = array('pageSize' => 1000,);
        }

        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => $criteria, 'pagination' => $pagination));

        $viewFile = $category->viewFile ? $category->viewFile : CatalogModule::$catalogCategoryViewFile;

        $this->render($viewFile, array('categoryItems' => $dataProvider->getData(), 'category' => $category, 'parentCat' => $parentCategory, 'maximalPriceValue' => $maximalPriceValue, 'pagination' => $dataProvider->pagination));
    }

    public function actionPromoView($promoId)
    {
        $this->layout = $this->module->baseLayout;

        $model = Promo::model()->findByPk($promoId);

        $this->render('promo', ['model' => $model]);
    }

    public function actionBuy($itemId)
    {

        Yii::import('catalog.models.forms.BuyForm');
        $buyFormModel = new BuyForm();

        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        $item = CatItem::model()->findByPk($itemId);

        if (isset($_POST['BuyForm'])) {

            $buyFormModel->attributes = $_POST['BuyForm'];
            if ($buyFormModel->validate()) {
                //отправка сообщения
                Yii::import('application.modules.callback.CallbackModule');

                $msg =
                    'Модель:' . $buyFormModel->model . '<br>' .
                    'Имя:' . $buyFormModel->name . '<br>' .
                    'Тел.:' . $buyFormModel->phone . '<br>' .
                    'Кол-во:' . $buyFormModel->count . '<br>' .
                    'Почта:' . $buyFormModel->email . '<br>' .
                    'Сообщение:' . $buyFormModel->msg . '
                    ';

                CallbackModule::addMessage($_SERVER['SERVER_NAME'] . ' - заказ', $msg, 'order', true);
                $this->render('buyOk', array('id' => $itemId, 'item' => $item, 'buyFormModel' => $buyFormModel));
            }
        }

        $this->render('buy', array('id' => $itemId, 'item' => $item, 'buyFormModel' => $buyFormModel));
    }

    /**
     * Отображение страницы корзины
     */
    public function actionBasket()
    {
        $this->layout = CatalogModule::$catalogCategoryViewLayout;
        $this->render('basket');
    }

    public function actionTest()
    {
        Yii::import('catalog.components.CBasketState');
        $basketState = new CBasketState();
        $basketState->printBasket();
    }

    public function actionModelsAndPrices()
    {
        $this->layout = CatalogModule::$catalogCategoryViewLayout;
        $this->render('ModelsAndPrices');
    }

    public function actionService()
    {
        $str = "<strong>1</strong><strong>1</strong><strong>1</strong><strong>1</strong>";
    }
}
//
//if(this.count > 0){
//    $(this.elementCheck).each(function(el){
//        el.attr('checked', 'checked').trigger('refresh');
//    });
//} else {
//    $(this.elementCheck).each(function(el){
//        el.removeAttr('checked').trigger('refresh');
//    });
//}
