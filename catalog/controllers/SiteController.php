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

    public function actionIndex() {
        $this->layout = CatalogModule::$catalogLayout;

        $categories = CatCategory::model()->findAll(array('condition' => 'level = 0', 'order' => '`order`'));

        $this->render('index', array('categories' => $categories));
    }

    public function actionItemView($catId = 0, $item = 0) {
        $this->layout = CatalogModule::$catalogItemViewLayout;
        $category = CatCategory::model()->findByPk($catId);
        $item = CatItem::model()->findByPk($item);
        //$dataProvider = new CActiveDataProvider('CatItemsToCat',array('criteria'=>array('select'=>'t.itemId','condition'=>'`t`.`catId` = '.$catId.'','with'=>'item','order'=>'t.order'))); 


        $this->render('itemView', array('item' => $item, 'category' => $category));
    }

    public function actionCategoryView($catId = 0) {
      
        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        $category = CatCategory::model()->findByPk($catId);

        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => array('select' => 't.itemId', 'condition' => '`t`.`catId` = ' . $catId . '', 'with' => 'item', 'order' => 't.order'),'pagination'=>array( 'pageSize'=>1000)));

        $this->render('categoryView', array('categoryItems' => $dataProvider->getData(), 'category' => $category));
    }

    public function actionRCategoryView($catId = 0) {
        
        $this->layout = CatalogModule::$catalogCategoryViewLayout;

        $category = CatCategory::model()->findByPk($catId);

        $catsIDs = $category->getAllCatChilds($catId);

        $iDsArray = array($catId);
        foreach ($catsIDs as $catData) {
            $iDsArray[] = $catData['id'];
        }

        $iDsStr = '(' . implode(',', $iDsArray) . ')';

        $items = CatItemsToCat::model()->findAll(array('condition' => '`catId` in ' . $iDsStr));
        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => array('select' => 't.itemId', 'condition' => '`t`.`catId` in ' . $iDsStr . '', 'with' => 'item', 'order' => 't.order', 'distinct' => true, 'group')));

        $this->render('categoryView', array('categoryItems' => $dataProvider->getData()));
    }

}