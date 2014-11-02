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

    public function actions(){
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
            ),
        );
    }

    public function actionIndex() {
        
        $this->layout = CatalogModule::$catalogLayout;

        $categories = CatCategory::model()->findAll(array('condition' => 'level = 0', 'order' => '`order`'));
        

        $this->render('index', array('categories' => $categories));
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
        $uri = $_SERVER['REQUEST_URI'];

        $item = CatItem::model()->findByPk($item);
        $this->layout = CatalogModule::$catalogItemViewLayout;
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

        $this->render('itemView', array('item' => $item, 'category' => $category));

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

        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        $category = CatCategory::model()->findByPk($catId);
        $maximalPriceValue = CatItem::model()->getItemWithMaximalPrice($catId);
        $parentCategory = null;
        if ($category->pid != "-1"){
            $parentCategory = CatCategory::model()->findByPk($category->pid);
        }

        $catsIDs = $category->getAllCatChilds($catId);

        $iDsArray = array($catId);
        foreach ($catsIDs as $catData) {
            $iDsArray[] = $catData['id'];
        }

        $iDsStr = '(' . implode(',', $iDsArray) . ')';
        $criteria = new CDbCriteria;

        $criteria->select = 't.itemId, t.catId';
        $criteria->condition = '`t`.`catId` in ' . $iDsStr . '';
        $criteria->with = array(
            'item'=>array(
                'condition'=>'published=1'
            )
        ); 
        $criteria->distinct = true;
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

        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => $criteria, 'pagination' => array('pageSize'=>1000)));


        $this->render('rCategoryView', array('categoryItems' => $dataProvider->getData(),'category'=>$category,'parentCat'=>$parentCategory, 'maximalPriceValue' => $maximalPriceValue));
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

    public function actionTest(){

    }

}
