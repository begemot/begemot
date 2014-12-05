<?php

class SiteController extends Controller
{
    
    public $layout='begemot.views.layouts.column2';

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
              $this->layout = PostModule::$postViewLayout;
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }
    /*
     *  Выводит статьи из всех, кроме перечисленных. По умолчанию 0 и 6
     *  передаем строку через с id разделов которые не надо выводить"_"
     */
     public function actionStopTags($stop='0_6') {

        $this->layout = PostModule::$postLayout;

        $idArray = explode('_',$stop);

        $condition = 'tag_id <> '.implode(" and tag_id<>",$idArray);

        $criteria = new CDbCriteria(array(
            'select' => '*',
            'distinct' => true,
            'order'=>'create_time,id',
            'condition'=>$condition
        ));


        $count = Posts::model()->count($criteria);

        $pages = new CPagination($count);
        // элементов на страницу
        $pages->pageSize = 10;
        $pages->applyLimit($criteria);

        $models = Posts::model()->published()->findAll($criteria);

        $dataProvider = new CActiveDataProvider('Posts');
        $this->render('index', array(
            'models' => $models,
            'pages' => $pages,
            'tag_id'=>null,
        ));
    }

    public function actionTagIndex($id=null) {

        $this->layout = PostModule::$postLayout;
        if ($id!=null){

            $tag = PostsTags::model()->findByPk($id);
            $this->pageTitle = $tag->title_seo;

            if (isset($_GET['page'])){
               $this->pageTitle .= ' - страница '.$_GET['page'];
            }

            $criteria = new CDbCriteria(array(
                    'select' => '*',
                    'distinct' => true,
                    'order'=>"id DESC",
                    'condition'=>'tag_id = '.$id
                ));

        } else{
            $criteria = new CDbCriteria(array(
                    'select' => '*',
                    'distinct' => true,
                    'order'=>"id DESC",
                    'condition'=>'tag_id <>0'
                ));
        }

        $count = Posts::model()->count($criteria);

        $pages = null;
        if ($this->module->limit) {
            $pages = new CPagination($count);
            $pages->pageSize = 10;
            $pages->applyLimit($criteria);
        }

        $models = Posts::model()->published()->findAll($criteria);

        if ($id!=null){
            $tagModel = PostsTags::model()->findByPk($id);
        } else{
            $tagModel = PostsTags::model()->findByPk(0);
        }

        $tags = PostsTags::model()->findAll(array('order'=>'`id`'));

        $dataProvider = new CActiveDataProvider('Posts');
        $this->render('index', array(
            'models' => $models,
            'pages' => $pages,
            'tagModel'=>$tagModel,
            'tag_id'=>$id,
            'tags'=>$tags
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin($tag_id=null) {


        
         $this->layout ='begemot.views.layouts.column2';
        $model = new Posts('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Posts']))
            $model->attributes = $_GET['Posts'];

        $this->render('admin', array(
            'model' => $model,
            'tag_id'=>$tag_id,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Posts::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'posts-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionTidy(){



        $post = '
        <p>Abzac 1</p>
        <p>Abzac 2</p>
        <p>Abzac 3</p>
        <p>Abzac 4</p>
        <p>Abzac 5</p>
        <p>Abzac 6</p>
        <p>Abzac 7</p>
        <p>Abzac 8</p>
        <p>Abzac 9</p>
        <p>Abzac 10</p>
        <p>Abzac 11</p>
        <p>Abzac 12</p>
        <p>Abzac 13</p>
        <p>Abzac 14</p>
        ';

        $images = array(
            0 =>
            array (
                'original' => '/files/pictureBox/catalogItem/101/0.jpg',
                'admin' => '/files/pictureBox/catalogItem/101/0_admin.jpg',
                'main' => '/files/pictureBox/catalogItem/101/0_main.jpg',
                'innerSmall' => '/files/pictureBox/catalogItem/101/0_innerSmall.jpg',
                'slider' => '/files/pictureBox/catalogItem/101/0_slider.jpg',
                'two' => '/files/pictureBox/catalogItem/101/0_two.jpg',
                'title' => 'Удобство пассажирских перевозок',
                'alt' => 'Электромобиль VOLTECO ALPHA L11S - находка туристического бизнеса',
            ),
            1 =>
            array (
                'original' => '/files/pictureBox/catalogItem/101/1.jpg',
                'admin' => '/files/pictureBox/catalogItem/101/1_admin.jpg',
                'main' => '/files/pictureBox/catalogItem/101/1_main.jpg',
                'innerSmall' => '/files/pictureBox/catalogItem/101/1_innerSmall.jpg',
                'slider' => '/files/pictureBox/catalogItem/101/1_slider.jpg',
                'two' => '/files/pictureBox/catalogItem/101/1_two.jpg',
                'title' => 'Открытый дизайн и эргономика кресел',
                'alt' => 'Электромобиль VOLTECO ALPHA L11S - современный стиль',
            ),
            2 => array (
                'original' => '/files/pictureBox/catalogItem/101/1.jpg',
                'admin' => '/files/pictureBox/catalogItem/101/1_admin.jpg',
                'main' => '/files/pictureBox/catalogItem/101/1_main.jpg',
                'innerSmall' => '/files/pictureBox/catalogItem/101/1_innerSmall.jpg',
                'slider' => '/files/pictureBox/catalogItem/101/1_slider.jpg',
                'two' => '/files/pictureBox/catalogItem/101/1_two.jpg',
                'title' => 'Открытый дизайн и эргономика кресел',
                'alt' => 'Электромобиль VOLTECO ALPHA L11S - современный стиль',
            ),
            3 =>
            array (
                'original' => '/files/pictureBox/catalogItem/101/0.jpg',
                'admin' => '/files/pictureBox/catalogItem/101/0_admin.jpg',
                'main' => '/files/pictureBox/catalogItem/101/0_main.jpg',
                'innerSmall' => '/files/pictureBox/catalogItem/101/0_innerSmall.jpg',
                'slider' => '/files/pictureBox/catalogItem/101/0_slider.jpg',
                'two' => '/files/pictureBox/catalogItem/101/0_two.jpg',
                'title' => 'Удобство пассажирских перевозок',
                'alt' => 'Электромобиль VOLTECO ALPHA L11S - находка туристического бизнеса',
            ),
            4 =>
            array (
                'original' => '/files/pictureBox/catalogItem/101/1.jpg',
                'admin' => '/files/pictureBox/catalogItem/101/1_admin.jpg',
                'main' => '/files/pictureBox/catalogItem/101/1_main.jpg',
                'innerSmall' => '/files/pictureBox/catalogItem/101/1_innerSmall.jpg',
                'slider' => '/files/pictureBox/catalogItem/101/1_slider.jpg',
                'two' => '/files/pictureBox/catalogItem/101/1_two.jpg',
                'title' => 'Открытый дизайн и эргономика кресел',
                'alt' => 'Электромобиль VOLTECO ALPHA L11S - современный стиль',
            ),
            5 => array (
                'original' => '/files/pictureBox/catalogItem/101/1.jpg',
                'admin' => '/files/pictureBox/catalogItem/101/1_admin.jpg',
                'main' => '/files/pictureBox/catalogItem/101/1_main.jpg',
                'innerSmall' => '/files/pictureBox/catalogItem/101/1_innerSmall.jpg',
                'slider' => '/files/pictureBox/catalogItem/101/1_slider.jpg',
                'two' => '/files/pictureBox/catalogItem/101/1_two.jpg',
                'title' => 'Открытый дизайн и эргономика кресел',
                'alt' => 'Электромобиль VOLTECO ALPHA L11S - современный стиль',
            ),
        );

        Yii::import('application.modules.begemot.components.tidy.TidyBuilder');

        $tidy = new TidyBuilder ( $post, $this->module->tidyConfig, $images);

        $tidy->renderText();


    }

}
