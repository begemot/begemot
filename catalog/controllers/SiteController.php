<?php

class SiteController extends Controller {

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

    public function actions(){
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

    public function actionIndex() {

        $this->layout = $this->module->baseLayout;



        $categories = CatCategory::model()->findAll(array('condition' => 'level = 0', 'order' => '`order`'));


        $this->render('index',['categories'=>$categories]);
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
    
    public function actionItemView($catId = 0, $item = 0) {

        if (!is_null($this->module->itemLayout)) {
            $this->layout = $this->module->itemLayout;
        } else{
            $this->layout = $this->module->baseLayout;
        }

        $uri = $_SERVER['REQUEST_URI'];

        $item = CatItem::model()->with('options')->findByPk($item);
//        $this->layout = CatalogModule::$catalogItemViewLayout;
        $category = CatCategory::model()->findByPk($item->catId);

        $hrefParams = array(
            'title'=>$category->name_t,
            'catId'=>$category->id,
            'itemName'=>$item->name_t,
            'item'=>$item->id,
        );

        $itemHref =  Yii::app()->urlManager->createUrl('catalog/site/itemView',$hrefParams);

        if ($itemHref!==$uri)
        {   
            $this->redirect($itemHref, true, 301);
        }

        $itemViewFile = $category->itemViewFile ? $category->itemViewFile : 'itemView';

        $this->render($itemViewFile, array('item' => $item, 'category' => $category));

    }

    public function actionCategoryView($catId = 0) {

        $this->layout = CatalogModule::$catalogCategoryViewLayout;
        $category = CatCategory::model()->findByPk($catId);
        $maximalPriceValue = CatItem::model()->getItemWithMaximalPrice($catId);
        $criteria = new CDbCriteria;
        
        $criteria->select = 't.itemId';
        $criteria->condition = '`t`.`catId` = ' . $catId . '';
        $criteria->with = array(
            'item'=>array(
                'condition'=>'published=1'
            )
        ); 
        $criteria->order = 'item.top DESC, t.order ASC';

        if (isset($_GET['sort'])) {
           $sort = ($_GET['sort'] == 'asc') ? 'asc' : 'desc';
           $criteria->order = 'item.price '.$sort;
        }
            
        if ( isset($_GET['priceMin']) && isset($_GET['priceMax']) ) {
           $priceMin = (int)$_GET['priceMin'];
           $priceMax = (int)$_GET['priceMax'];

           $criteria->addBetweenCondition('price', $priceMin,$priceMax);
        }
        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => $criteria,'pagination'=>array('pageSize'=>1000)));

       // $dataProvider=CatItemsToCat::model()->published()->with('item')->findAll();top

        $this->render('categoryView', array('categoryItems' => $dataProvider->getData(), 'category' => $category, 'maximalPriceValue' => $maximalPriceValue));

    }

    public function actionRCategoryView($catId = 0) {

//        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        $this->layout = $this->module->baseLayout;

        $category = CatCategory::model()->findByPk($catId);
        $maximalPriceValue = CatItem::model()->getItemWithMaximalPrice($catId);
        $parentCategory = null;
        if ($category && $category->pid != "-1"){
            $parentCategory = CatCategory::model()->findByPk($category->pid);
        }

        if($category->layout){
            $this->layout = $category->layout;
        }
        //$catsIDs = $category->getAllCatChilds($catId);

//        $iDsArray = array($catId);
//        foreach ($catsIDs as $catData) {
//            $iDsArray[] = $catData['id'];
//        }

       // $iDsStr = '(' . implode(',', $iDsArray) . ')';
        $criteria = new CDbCriteria;

        $criteria->select = 't.itemId, t.catId';
        $criteria->condition = '`t`.`catId` = ' . $catId . '';

        $criteria->with = array(
            'item'=>array(
                'condition'=>'published=1'
            )
        );

       // $criteria->group = 'item.id';
        $criteria->distinct = true;
        $criteria->order = 't.order ';

        if (isset($_GET['sort'])) {
           $sort = ($_GET['sort'] == 'asc') ? 'asc' : 'desc';
           $criteria->order = 'item.price '.$sort;
        }

        if (isset($_GET['sortByCustomField'])) {
            $sort = ($_GET['sort'] == 'asc') ? 'asc' : 'desc';
           $criteria->order = 'item.' . $_GET['sortByCustomField'] . ' '.$sort;
        }
            
        if ( isset($_GET['priceMin']) && isset($_GET['priceMax']) ) {
           $priceMin = (int)$_GET['priceMin'];
           $priceMax = (int)$_GET['priceMax'];

           $criteria->addBetweenCondition('price', $priceMin,$priceMax);
        }

        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => $criteria, 'pagination' => array('pageSize'=>1000)));

        $viewFile = $category->viewFile ? $category->viewFile : CatalogModule::$catalogCategoryViewFile;

        $this->render($viewFile, array('categoryItems' => $dataProvider->getData(),'category'=>$category,'parentCat'=>$parentCategory, 'maximalPriceValue' => $maximalPriceValue));
    }

    public function actionPromoView ($promoId){
        $this->layout = $this->module->baseLayout;

        $model = Promo::model()->findByPk($promoId);

        $this->render('promo',['model'=>$model]);
    }

    public function actionBuy ($itemId){

        Yii::import('catalog.models.forms.BuyForm');
        $buyFormModel = new BuyForm();

        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        $item = CatItem::model()->findByPk($itemId);

        if(isset($_POST['BuyForm'])){

            $buyFormModel->attributes = $_POST['BuyForm'];
            if ($buyFormModel->validate()){
            //отправка сообщения
                Yii::import('application.modules.callback.CallbackModule');

                $msg =
                    'Модель:'.$buyFormModel->model.'<br>'.
                    'Имя:'.$buyFormModel->name.'<br>'.
                    'Тел.:'.$buyFormModel->phone.'<br>'.
                    'Кол-во:'.$buyFormModel->count.'<br>'.
                    'Почта:'.$buyFormModel->email.'<br>'.
                    'Сообщение:'.$buyFormModel->msg.'
                    ';
                
                CallbackModule::addMessage($_SERVER['SERVER_NAME'].' - заказ',$msg,'order',true);
                $this->render('buyOk',array('id'=>$itemId,'item'=>$item,'buyFormModel'=>$buyFormModel));
            }

        }

        $this->render('buy',array('id'=>$itemId,'item'=>$item,'buyFormModel'=>$buyFormModel));
    }

    /**
     * Отображение страницы корзины
     */
    public function actionBasket(){
        $this->layout = CatalogModule::$catalogCategoryViewLayout;
        $this->render('basket');
    }

    public function actionTest(){
        Yii::import('catalog.components.CBasketState');
        $basketState = new CBasketState();
        $basketState->printBasket();
    }

    public function actionModelsAndPrices()
    {
        $this->layout = CatalogModule::$catalogCategoryViewLayout;
        $this->render('ModelsAndPrices');
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
