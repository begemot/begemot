<?php
class CBOrderModelBehavior extends CActiveRecordBehavior {

    public $orderAttribute = 'order';

    public $modelOrder = 0;

    public function events(){
        return array(
            'onBeforeSave' => 'orderBeforeSave',
        );
    }

    public function orderUp(){
        return;
    }

    public function orderDown(){
        return;
    }

    public function afterFind ($event){
       $this->modelOrder = $this->getOwner()->order;
    }
    public function beforeSave ($event=null){

        parent::beforeSave($event=null);
        if ($this->getOwner()->isNewRecord){
          if (isset($this->getOwner()->order)) {

            $criteria = new CDbCriteria;

            $criteria->select = 'MAX(`order`) as `order`';


            $order = $this->getOwner()->find($criteria);

            $this->getOwner()->order = $order->order+1;

            return $order->order+1;
          }
        }
        return 0;
    }

    public function orderBeforeSave(){
        $this->beforeSave();
    }

    public function getLastOrderValue()
    {
      return $this->beforeSave();
    }
}

?>
