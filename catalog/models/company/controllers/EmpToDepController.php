<?php

class EmpToDepController extends Controller
{

    public $layout = 'begemot.views.layouts.column2';

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('adminForDepart','orderUp','orderDown'),
                'users' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function behaviors()
    {
        return array(
            'CBOrderControllerBehavior' => array(
                'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
                'groupName' => 'depId'
            )
        );
    }

    public function actionAdminForDepart($depId)
    {
//        $model = new CompanyEmployee('search');
//        $model->unsetAttributes();  // clear any default values
//        if (isset($_GET['CompanyEmployee']))
//            $model->attributes = $_GET['CompanyEmployee'];
        $cDbCriteria = ['condition'=>'`depId`='.$depId];
        $empModel = CompanyEmpToDep::model();
        $empModel->attributes = ['depId'=>$depId];
        $empModel->dbCriteria->order = '`order`';
        $this->render('adminForDepart', array(
            'model' => $empModel->with('emp'),
        ));
    }

    public function loadModel($id)
    {
        $model = CompanyEmpToDep::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }



    public function actionOrderUp($id)
    {
//        $model = $this->loadModel($id);


        $this->orderUp($id);

    }

    public function actionOrderDown($id)
    {
//        $model = $this->loadModel($id);



        $this->orderDown($id);
    }

}