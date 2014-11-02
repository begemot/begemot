<?php

class CatItemController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = 'begemot.views.layouts.column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('delete', 'create', 'update', 'index', 'view', 'deleteItemToCat', 'tidyItemText'),
                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {

        CatalogModule::checkEditAccess();

        $model = new CatItem;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['CatItem'])) {

            $model->attributes = $_POST['CatItem'];
            if ($model->save())
                $this->redirect(array('update', 'id' => $model->id));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id, $tab = 'data')
    {
        $model = $this->loadModel($id);

        CatalogModule::checkEditAccess($model->authorId);


        if (isset($_GET['setMainCat'])) {
            $model->catId = $_GET['setMainCat'];
            $model->save();
        }

        if (isset($_POST['saveItemsToItems']) && isset($_POST['options'])) {
            CatItemsToItems::model()->deleteAll(array("condition" => 'itemId=' . $id));


            if (count($_POST['options'])) {
                foreach ($_POST['options'] as $itemId) {
                    $item = new CatItemsToItems();

                    $item->itemId = $id;
                    $item->toItemId = $itemId;

                    $item->save();
                }

            }

        } else if (isset($_POST['saveItemsToItems']) && !isset($_POST['options'])) {
            CatItemsToItems::model()->deleteAll(array("condition" => 'itemId=' . $id));
        }

        if (isset($_POST['CatItem'])) {

            $model->attributes = $_POST['CatItem'];
            $model->save();
            //	$this->redirect(array('view','id'=>$model->id));
        }

        $itemToCat = new CatItemsToCat;
        $testForm = new CForm('catalog.models.forms.catToItemForm', $itemToCat);


        if ($testForm->submitted('catToItemSubmit') && $testForm->validate()) {
            $itemToCat->attributes = $_POST['CatItemsToCat'];
            $itemToCat->save();

            if ($itemToCat->item->catId == 0) {
                $itemToCat->item->catId = $itemToCat->catId;
                $itemToCat->item->save();
            }

        }

        $this->render('update', array(
            'model' => $model,
            'tab' => $tab,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);

        CatalogModule::checkEditAccess($model->authorId);

        $model->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {

        $dataProvider = new CActiveDataProvider('CatItem', array('criteria' => array('order' => '`id` desc')));

        $dataProvider = new CatItem('search');
        if (isset($_GET['CatItem']))
            $dataProvider->Attributes = $_GET['CatItem'];
        $this->render('index', array(
            'dataProvider' => $dataProvider,

        ));

    }


    public function actionDeleteItemToCat($catId, $itemId)
    {

        $model = $this->loadModel($itemId);

        CatalogModule::checkEditAccess($model->authorId);

        if (Yii::app()->request->isAjaxRequest) {

            CatItemsToCat::model()->deleteAll(array('condition' => '`catId`=' . $catId . ' and `itemId`=' . $itemId));

            $catItem = CatItem::model()->findByPk($itemId);
            if ($catItem->catId == $catId) {

                $catItem->catId = 0;

                $models = CatItemsToCat::model()->findAll(array('condition' => '`itemId`=' . $itemId));

                if (count($models) > 0) {

                    $model = $models[0];

                    $catItem->catId = $model->catId;

                }


                $catItem->save();

            }

        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return CatItem the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = CatItem::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CatItem $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'cat-item-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionTidyItemText($id)
    {

        $model = $this->loadModel($id);

        Yii::import('application.modules.pictureBox.components.PBox');

        $pbox = new PBox('catalogItem', $id);

        $images = $pbox->pictures;
        //print_r($pbox->pictures);
        //return;
        $text = $model->text;

        Yii::import('application.modules.begemot.components.tidy.TidyBuilder');

        $this->module->tidyleadImage != 0 ? $leadImage = 1 : $leadImage = 0;

        $tidy = new TidyBuilder ($model->text, $this->module->tidyConfig, $images, $leadImage);

        $model->text = $tidy->renderText();

        $model->save();

        $this->redirect(array('/catalog/catItem/update', 'id' => $model->id,));
    }
}