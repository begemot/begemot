<?php

class SiteController extends Controller {

    public function actions(){
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
            ),
        );
    }

    public function actionIndex() {
        
        $this->layout = CatalogModule::$catalogLayout;

        $categories = CatCategory::model()->findAll(array('condition' => 'level = 0', 'order' => '`order`'));
        

        $this->render('index', array('categories' => $categories));
    }

    public function actionDepart($departId){

        $this->layout = '//layouts/clear';
        $depart = CompanyDepart::model()->findByPk($departId);

        $this->render('depart',['depart'=>$depart]);
    }

    public function actionEmployes(){

        $this->layout = '//layouts/clear';

        $this->render('//site/employes');
    }

    public function actionEmp($empId){

        $this->layout = '//layouts/clear';

        $this->render('//site/employe',['id'=>$empId]);
    }
}
