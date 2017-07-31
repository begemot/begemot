<?php

class SiteController extends Controller {

    public $layout = '//layouts/clear';

    public function actionIndex() {
        

        $this->render('index');
    }




}
