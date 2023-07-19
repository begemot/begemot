<?php

class ModelsWidget extends CWidget {
    
    public $limit = 3;
    public $onlyTop = true;
    public $order = false;
    public $categoryId = null;
    
    public function run(){
        Yii::import('catalog.models.CatItemsToCat');
        Yii::import('catalog.models.CatItem');
        Yii::import('catalog.models.CatCategory');

        $criteria = array();

        if ($this->onlyTop){
            $criteria =  array('condition'=>'`item`.`published`=1 AND `top`=1', 'with'=>array('item','cat'));
            //$criteria->distinct = true;
        } elseif(!is_null($this->categoryId)){
            $criteria =  array('condition'=>'`item`.`published`=1 AND `t`.`catId`='.$this->categoryId, 'with'=>array('item','cat'));

        }
        else {
            $criteria =  array('condition'=>'`item`.`published`=1', 'with'=>array('category'));
        }

        if ($this->order){
            $criteria['order']= '`t`.`order`';
        }

        $dataProvider = new CActiveDataProvider('CatItemsToCat', array('criteria' => $criteria,'pagination'=>array('pageSize'=>$this->limit)));

        $this->render('index',array('items'=>$dataProvider->getData()));

    }
    
}

?>
