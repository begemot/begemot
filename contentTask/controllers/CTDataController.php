<?php


class CTDataController extends Controller
{

    public function filters() {
        return array(
            'ajaxOnly + getTypes',
        );
    }

    public function actionGetTypes(){
//        if(Yii::app()->request->isAjaxRequest){

            $CTTypes = CTDataProvider::getTypes();

            echo json_encode($CTTypes);
//        }
    }


}