<?php

class PromoWidget extends CWidget {
    
    public $categoryId = null;
    public $itemId = null;
    public $viewFileName = 'promoIndex';

    
    public function run(){


        Yii::import('catalog.models.Promo');
        Yii::import('catalog.models.PromoRelation');
        Yii::import('catalog.models.PromoTypeEnum');
        Yii::import('catalog.models.CatItem');

        $criteria = new CDbCriteria();

        $conditionArray = [];

        $categoryCondition = '';

        if ($this->categoryId!==null){
            $categoryCondition = '(targetId=:categoryId and type=:categoryType)';
            $criteria->params[':categoryId']=$this->categoryId;
            $criteria->params[':categoryType']=PromoTypeEnum::TO_CATEGORY;
            $conditionArray[]=$categoryCondition;
        }

        $itemCondition = '';

        if ($this->itemId!==null){
            $itemCondition = '(targetId=:itemId and type=:itemType)';
            $criteria->params[':itemId']=$this->itemId;
            $criteria->params[':itemType']=PromoTypeEnum::TO_POSITION;
            $conditionArray[]=$itemCondition;
        }

        $criteria->condition = implode(' or ',$conditionArray);
        $criteria->order = 'promo.order ASC';

        $data = PromoRelation::model()->with('promo')->findAll($criteria);

        foreach ($data as $key=>$dataItem){
            if (!$dataItem->promo->published){
                unset($data[$key]);
            }
        }

        if (count($data)>0)
            $this->render($this->viewFileName,array('promos'=>$data));
        
    }
    
}

?>
