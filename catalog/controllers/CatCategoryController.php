<?php

class CatCategoryController extends Controller
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
        );
    }

    public function behaviors()
    {
        return array(
            'CBOrderControllerBehavior' => array(
                'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
                'groupName' => 'pid'
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
                'actions' => array('admin', 'delete', 'orderUp', 'orderDown', 'create', 'update', 'index', 'view','makeCopy','tidyPost'),

                'expression' => 'Yii::app()->user->canDo("Catalog")'

            ),
            array('deny', // deny all users
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
     * Создание копии позиций катлога
     */
    public function actionMakeCopy()
    {

        if (isset($_POST['makeItemsCopy']) && isset($_POST['items'])) {
            if (count($_POST['items'])) {
                foreach ($_POST['items'] as $itemId) {
                    $itemOriginal = CatItem::model()->findByPk($itemId);

                    if ($itemOriginal){

                        $itemCopy = new CatItem();
                        $table = $itemOriginal->getMetaData()->tableSchema;

                        $columns = $table->columns;
                        foreach ($columns as $column){

                            $columnName = $column->name;

                            if ($columnName==$table->primaryKey) continue;
                            if ($columnName=='published') {$itemCopy->$columnName = 0;continue;};
                            $itemCopy->$columnName = $itemOriginal->$columnName;

                        }

                        $itemCopy->isNewRecord = true;
                        $itemCopy->insert();

                        $lastId = Yii::app()->db->getLastInsertId();

                        //Копируем изображения
                        Yii::import('pictureBox.components.PBox');
                        $PBox = new PBox('catalogItem',$lastId);

                        $galleryId = 'catalogItem';

                        $originalPBox = new PBox($galleryId,$itemId);
                        $newPBox = new PBox($galleryId,$lastId);

                        $originalDataDir = dirname($originalPBox->dataFile);
                        $destanationDataDir = dirname($newPBox->dataFile);

                        if (file_exists($destanationDataDir)){
                            CFileHelper::removeDirectory($destanationDataDir);
                        }

                        mkdir ($destanationDataDir);

                        $files = glob ($originalDataDir.'/*');

                        foreach ($files as $file){

                            $file1 = $file;
                            $file2 = dirname($file).'/../'.$lastId.'/'.basename($file);
                            copy($file1,$file2);
                        }
                        //меняем все пути в файле-оглавлении
                        $configFilePath = dirname($file).'/../'.$lastId.'/data.php';
                        $configFileContentArray = file($configFilePath);


                        $resultFile = '';

                        foreach ($configFileContentArray as $configFileLine){
                            $resultFile .= str_replace('files/pictureBox/catalogItem/'.$itemId,'files/pictureBox/catalogItem/'.$lastId,$configFileLine);

                        }

                        file_put_contents($configFilePath,$resultFile);

                        //меняем все пути в файле избранных изображений
                        $configFilePath = dirname($file).'/../'.$lastId.'/favData.php';
                        if (file_exists($configFilePath)){

                            $configFileContentArray = file($configFilePath);


                            $resultFile = '';

                            foreach ($configFileContentArray as $configFileLine){
                                $resultFile .= str_replace('files/pictureBox/catalogItem/'.$itemId,'files/pictureBox/catalogItem/'.$lastId,$configFileLine);

                            }

                            file_put_contents($configFilePath,$resultFile);
                        }



                        //Копируем привязки к разделам, если нужно
                        if (isset($_POST['mode']) && $_POST['mode']=='catOfOriginal'){
                            $CatItemsRelations = CatItemsToCat::model()->findAll('itemId = '.$itemId);

                            foreach ($CatItemsRelations as $CatItemsToCat){
                                $newCatItemToCat = new CatItemsToCat();
                                $newCatItemToCat->catId = $CatItemsToCat->catId;
                                $newCatItemToCat->itemId = $lastId;
                                $newCatItemToCat->save();
                            }
                        }


                    }
                }
            }
        }

        $this->render('copy');
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new CatCategory;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['CatCategory'])) {
            $model->attributes = $_POST['CatCategory'];
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
    public function actionUpdate($id, $tab = 'data')
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['CatCategory'])) {
            $model->attributes = $_POST['CatCategory'];
            $model->save();
            //$this->redirect(array('view','id'=>$model->id));
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
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            if ($this->loadModel($id)->delete()) {

                $filename = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogCategory/' . $id;
                if (file_exists($filename)) {
                    Yii::import('begemot.BegemotModule');
                    BegemotModule::fullDelDir($filename);
                }
            }
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $filename = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogCategory/46';
        if (file_exists($filename)) {
            Yii::import('begemot.BegemotModule');
            BegemotModule::fullDelDir($filename);
        }
//		$dataProvider=new CActiveDataProvider('CatItem');
//		$this->render('admin',array(
//			'dataProvider'=>$dataProvider,
//		));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin($pid = -1)
    {

        $model = new CatCategory('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['CatCategory']))
            $model->attributes = $_GET['CatCategory'];

        $this->render('admin', array(
            'model' => $model,
            'pid' => $pid
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = CatCategory::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }


    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'cat-category-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionOrderUp($id)
    {
        $model = $this->loadModel($id);
        $orderModel = $model->getCategory($id);

        $this->groupId = $orderModel['pid'];
        $this->orderUp($id);
    }

    public function actionOrderDown($id)
    {
        $model = $this->loadModel($id);
        $orderModel = $model->getCategory($id);
        $this->groupId = $orderModel['pid'];
        $this->orderDown($id);
    }

    public function actionTidyPost($id)
    {

        $model = $this->loadModel($id);

        Yii::import('application.modules.pictureBox.components.PBox');

        $pbox = new PBox('catalogCategory', $id);

        $images = $pbox->pictures;

        $text = $model->text;

        Yii::import('application.modules.begemot.components.tidy.TidyBuilder');

        $this->module->tidyleadImage != 0 ? $leadImage = 1 : $leadImage = 0;

        $tidy = new TidyBuilder ($model->text, $this->module->tidyConfig, $images, $leadImage);

        $model->text = $tidy->renderText();

        $model->save();

        $this->redirect(array('/catalog/catCategory/update', 'id' => $model->id,));

    }
}
