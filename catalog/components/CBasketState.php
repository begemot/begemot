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

    private $basketData = [];
    private $session;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
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

    public function newCatIdCount($catId, $newCount)
    {
        if (isset($this->basketData['items'][$catId])){

            $this->basketData['items'][$catId]['count'] = $newCount;
            $this->saveData();
        }
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