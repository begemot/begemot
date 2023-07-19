<?php

class PromoController extends Controller
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

    public function behaviors(){
            return array(
                    'CBOrderControllerBehavior' => array(
                            'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
                    )
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
                'actions' => array('delete','orderUp', 'orderDown', 'create', 'update', 'index', 'view', 'admin','deletePromoToCat','deletePromoToPosition'),
                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionOrderUp($id){
        $model = $this->loadModel($id);

        $this->orderUp($id);

    }
    
    public function actionOrderDown($id){
        $model = $this->loadModel($id);

        $this->orderDown($id);
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
        $model = new Promo;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Promo'])) {
            $model->attributes = $_POST['Promo'];
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

        if (isset($_POST['Promo'])) {
            $model->attributes = $_POST['Promo'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        /**
         * Это обработка формы прикрепления раздела к акции
         * со вкладки "разделы"
         */

        $promoToCat = new PromoRelation;
        $promoToCatForm = new CForm('catalog.models.forms.promoToCatForm', $promoToCat);


        if ($promoToCatForm->submitted('promoToCatSubmit') && $promoToCatForm->validate()) {
            $promoToCat->attributes = $_POST['PromoRelation'];
            $promoToCat->save();
            $this->redirect(array('update','id'=>$id,'tab'=>'cat'));
        }

        /**
         * Это обработка прикрепления акции к id позиции в каталоге
         */
        $promoToItem = new PromoRelation;
        $promoToItemForm = new CForm('catalog.models.forms.promoToItemForm', $promoToItem);

        if ($promoToItemForm->submitted('promoToItemSubmit') && $promoToItemForm->validate()) {
            $promoToItem->attributes = $_POST['PromoRelation'];
            $promoToItem->save();
            $this->redirect(array('update','id'=>$id,'tab'=>'positions'));
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
    public function actionDeletePromoToCat($catId, $promoId)
    {

        if (Yii::app()->request->isAjaxRequest) {

            $model = PromoRelation::model()->find(array('condition'=>'targetId='.$catId.' and promoId='.$promoId.' and type='.PromoTypeEnum::TO_CATEGORY));

            $model->delete();

        }
    }
    /**
     *
     * Аякс-запросом удаляем связь между позицией каталога и акцией
     *
     * @param $itemId
     * @param $promoId
     * @throws CHttpException
     */
    public function actionDeletePromoToPosition($itemId, $promoId)
    {

        if (Yii::app()->request->isAjaxRequest) {

            $model = PromoRelation::model()->find(array('condition'=>'targetId='.$itemId.' and promoId='.$promoId.' and type='.PromoTypeEnum::TO_POSITION));

            $model->delete();

        }
    }
    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Promo');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Promo('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Promo']))
            $model->attributes = $_GET['Promo'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Promo the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Promo::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Promo $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'promo-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
