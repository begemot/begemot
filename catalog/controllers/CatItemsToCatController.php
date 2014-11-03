<?php

class CatItemsToCatController extends Controller
{
    	public $layout='begemot.views.layouts.column2';
        
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
                'actions'=>array('admin'),
                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete','orderUp','orderDown'),
                'expression' => 'Yii::app()->user->isAdmin()'
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));

	}
        
    public function behaviors(){
            return array(
                    'CBOrderControllerBehavior' => array(
                            'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
                            'groupName'=>'catId'
                    )
            );
    }
    
    public function actionOrderUp($id){
        $model = $this->loadModel($id);
  
        $this->groupId = $model->catId;
        $this->orderUp($id);

    }
    
    public function actionOrderDown($id){
        $model = $this->loadModel($id);
  

        $this->groupId = $model->catId;
        $this->orderDown($id);
    } 
	
    public function actionAdmin($id)
	{

            $dataProvider = new CActiveDataProvider('CatItemsToCat',array('criteria'=>array('condition'=>'`t`.`catId`='.$id.'','with'=>'item','order'=>'t.order')));


            $model = new CatItemsToCat('search');
            $model->unsetAttributes(); 

            if(isset($_GET['CatItemsToCat']))
               $model->attributes=$_GET['CatItemsToCat'];

            $this->render('admin',array(
                'dataProvider'=>$dataProvider,
                'category'=> CatCategory::model()->findByPk($id),
            ));
	}        
        
	public function loadModel($id)
	{
		$model= CatItemsToCat::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
