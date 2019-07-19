<?php

/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 15.12.2018
 * Time: 0:19
 */
class CTAdminController extends Controller
{
    public $layout='begemot.views.layouts.column2';
    public function actionSearch($taskId){
        $this->render('search',['taskId'=>$taskId]);
    }

}