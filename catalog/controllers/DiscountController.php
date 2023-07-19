<?php

class DiscountController extends Controller
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
                'actions' => array('delete', 'create', 'update', 'index', 'view', 'admin','deleteDiscountToCat','deleteDiscountToPosition'),
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
        $model = new Discount;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Discount'])) {
            $model->attributes = $_POST['Discount'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
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
    public function actionUpdate($id, $tab = null)
    {
        $model = $this->loadModel($id);


        /**
         * Это обработка первой вкладки
         */

        if (isset($_POST['Discount'])) {
            $model->attributes = $_POST['Discount'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        /**
         * Это обработка формы прикрепления раздела к скидке
         * со вкладки "разделы"
         */

        $discountToCat = new DiscountRelation;
        $discountToCatForm = new CForm('catalog.models.forms.discountToCatForm', $discountToCat);


        if ($discountToCatForm->submitted('discountToCatSubmit') && $discountToCatForm->validate()) {
            echo "ok";
            $discountToCat->attributes = $_POST['DiscountRelation'];
            $discountToCat->save();
            $this->redirect(array('update','id'=>$id,'tab'=>'cat'));
        }

        /**
         * Это обработка прикрепления скидки к id позиции в каталоге
         */
        // $discountToItem = new DiscountRelation;
        // $discountToItemForm = new CForm('catalog.models.forms.discountToItemForm', $discountToItem);

        // if ($discountToItemForm->submitted('discountToItemSubmit') && $discountToItemForm->validate()) {
        //     $discountToItem->attributes = $_POST['DiscountRelation'];
        //     $discountToItem->save();
        //     $this->redirect(array('update','id'=>$id,'tab'=>'positions'));
        // }

        if (isset($_POST['saveItemsToDiscount'])) {
            DiscountRelation::model()->deleteAll(array("condition" => "type=2 AND discountId=" . $id));

            if (isset($_POST['items'])) {
                foreach ($_POST['items'] as $itemId) {
                    echo "OK";
                    $item = new DiscountRelation();

                    $item->discountId = $id;
                    $item->targetId = $itemId;
                    $item->type= 2;

                    $item->save();
                    
                }

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
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     *
     * Аякс-запросом удаляем связь между категорией каталога и акцией
     *
     * @param $catId
     * @param $itemId
     * @throws CHttpException
     */
    public function actionDeleteDiscountToCat($catId, $discountId)
    {

        if (Yii::app()->request->isAjaxRequest) {

            $model = DiscountRelation::model()->find(array('condition'=>'targetId='.$catId.' and discountId='.$discountId.' and type='.DiscountTypeEnum::TO_CATEGORY));

            $model->delete();

        }
    }
    /**
     *
     * Аякс-запросом удаляем связь между позицией каталога и акцией
     *
     * @param $itemId
     * @param $discountId
     * @throws CHttpException
     */
    public function actionDeleteDiscountToPosition($itemId, $discountId)
    {

        if (Yii::app()->request->isAjaxRequest) {

            $model = DiscountRelation::model()->find(array('condition'=>'targetId='.$itemId.' and discountId='.$discountId.' and type='.DiscountTypeEnum::TO_POSITION));

            $model->delete();

        }
    }
    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Discount');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Discount('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Discount']))
            $model->attributes = $_GET['Discount'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Discount the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Discount::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Discount $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'discount-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
