<?php
/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 18.01.2018
 * Time: 1:07
 */

class BasketControllController extends Controller
{
    public function actionBasketAjaxAddItem($id)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            $basketState->addCatId($id);
            echo true;
        }
    }

    public function actionBasketAjaxRemoveItem($id)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            $basketState->removeCatId($id);
            echo true;
        }
    }

    public function actionBasketAjaxSetShipment($shipmentPrice,$shipmentId)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            $basketState->setShipment($shipmentPrice,$shipmentId);
            echo true;
        }
    }

    public function actionAjaxGetBasketCount(){
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            echo json_encode ($basketState->count());
        }
    }

    public function actionAjaxGetBasketPriceSum(){
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            echo json_encode ($basketState->priceSum());
        }
    }

    public function actionAjaxBasketAddCount($itemId){
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            $basketState->addCount($itemId);
            echo json_encode($basketState->getItemCount($itemId));
        }
    }
    public function actionAjaxBasketDinCount($itemId){
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            $basketState->dinCount($itemId);
            echo json_encode($basketState->getItemCount($itemId));
        }
    }

    public function actionAjaxBasketSetCount($itemId,$count){
        if (Yii::app()->request->isAjaxRequest) {
            $basketState = new CBasketState();
            $basketState->setCount($itemId,$count);
            echo json_encode($basketState->getItemCount($itemId));
        }
    }
}