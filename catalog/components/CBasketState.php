<?php
/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 12.12.2017
 * Time: 2:35
 */

class CBasketState
{

    const __BASKET_OFFSET_NAME__ = 'basketOffset2';
    const  __MAIN_JS_FILE__ = '/protected/modules/catalog/assets/js/basketState/Basket.js';

    private $basketData = [];
    private $session;

    public function __construct()
    {
        $this->init();
    }

    public function getItems()
    {
        return $this->basketData['items'];
    }

    private function init()
    {
        Yii::app()->clientScript->registerScriptFile($this::__MAIN_JS_FILE__,2);

        $session = new CHttpSession;
        $session->open();

        if ($session->offsetExists($this::__BASKET_OFFSET_NAME__)) {
            $this->basketData = $session[$this::__BASKET_OFFSET_NAME__];
        } else {
            $session[$this::__BASKET_OFFSET_NAME__] = [
                'items' => []
            ];
        }
        $this->session = $session;
    }

    private function saveData()
    {
        $this->session[$this::__BASKET_OFFSET_NAME__] = $this->basketData;
    }

    public function addCatId($catId, $count = 1)
    {
        $this->basketData['items'][$catId] = ['count' => $count];
        $this->saveData();
    }

    public function removeCatId($catId)
    {
        unset($this->basketData['items'][$catId]);
        $this->saveData();
    }

    public function addCount($catId)
    {
        $this->basketData['items'][$catId]['count']++;
        $this->saveData();
    }

    public function dinCount($catId)
    {
        $this->basketData['items'][$catId]['count']--;
        if ($this->basketData['items'][$catId]['count']<0)$this->basketData['items'][$catId]['count']=0;
        $this->saveData();
    }

    public function setCount($catId,$count)
    {
        $this->basketData['items'][$catId]['count']=$count;
        $this->saveData();
    }
    public function newCatIdCount($catId, $newCount)
    {
        if (isset($this->basketData['items'][$catId])) {

            $this->basketData['items'][$catId]['count'] = $newCount;
            $this->saveData();
        }
    }

    public function count(){
        $count = 0;
        if (isset($this->basketData['items'])) {
            $count = count($this->basketData['items']);
        }
        return $count;
    }

    public function isExistInBasket($itemId){

        return isset($this->basketData['items'][$itemId]);
    }

    public function priceSum(){
        $priceSum = 0;
        if (isset($this->basketData['items'])) {
            $priceSum = 0;
            foreach ($this->basketData['items'] as $itemId=>$data){
                $price = CatItem::model()->findByPk($itemId)->price;
                $priceSum +=$price*$data['count'];
            }
//            $count = count($this->basketData['items']);
        }
        return  number_format (  $priceSum ,0, '', ' ');
    }

    public function getItemCount($itemId){
        return $this->basketData['items'][$itemId]['count'];
    }

    public function printBasket()
    {
//        $this->newCatIdCount(143,10);
//        $this->addCatId(143);
        echo '<pre>';
        print_r($this->basketData);
        echo '</pre>';
        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
    }


}