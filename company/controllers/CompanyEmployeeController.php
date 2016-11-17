<?php

class CompanyEmployeeController extends Controller
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

    public function behaviors()
    {
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
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'ajaxEmpToDep'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete','orderDown','orderUp'),
                'users' => array('admin'),
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
        $model = new CompanyEmployee;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['CompanyEmployee'])) {
            $model->attributes = $_POST['CompanyEmployee'];
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
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['CompanyEmployee'])) {
            $model->attributes = $_POST['CompanyEmployee'];
            $model->save();

        }

        $this->render('update', array(
            'model' => $model,
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
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('CompanyEmployee');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new CompanyEmployee('search');
        $model->unsetAttributes();  // clear any default values
        $model->dbCriteria->order="`t`.`order` ASC";
        if (isset($_GET['CompanyEmployee']))
            $model->attributes = $_GET['CompanyEmployee'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }


    public function actionAjaxEmpToDep($empId, $depId)
    {
        $params = [
            'empId' => $empId,
            'depId' => $depId,
        ];
        $companyEmpToDep = CompanyEmpToDep::model()->findByAttributes($params);

        if (is_null($companyEmpToDep)) {
            $companyEmpToDep = new CompanyEmpToDep();
            $companyEmpToDep->empId = $empId;
            $companyEmpToDep->depId = $depId;

            $criteria = new CDbCriteria;
            $criteria->condition = "`depId` = " . $depId;
            $criteria->select = 'MAX(`order`) as `order`';


            $order = CompanyEmpToDep::model()->find($criteria);
            $order = $order->order;
            $companyEmpToDep->order = $order + 1;

            $companyEmpToDep->save();
        } else {
            $companyEmpToDep->delete();
        }

    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return CompanyEmployee the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = CompanyEmployee::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CompanyEmployee $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'company-employee-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionOrderUp($id)
    {

        $this->orderUp($id);

    }

    public function actionOrderDown($id)
    {

        $this->orderDown($id);
    }

}
