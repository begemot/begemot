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
            //            'CBOrderControllerBehavior' => array(
            //                'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
            //                'groupName' => 'pid'
            //            )
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

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array('ajaxCategoryCreate', 'admin', 'moveCat', 'delete', 'orderUp', 'orderDown', 'create', 'update', 'index', 'view', 'makeCopy', 'tidyPost', 'directMakeCopyOrMove', 'massItemsToCategoriesConnect', 'catManage', 'echoJsonCategories'),

                'expression' => 'Yii::app()->user->canDo("Catalog")'


            ),
            array(
                'deny', // deny all users
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

            $copyParams = [
                'mode' => 'copy',
                'catOfOriginal' => $_POST['mode'] == 'catOfOriginal'
            ];

            $this->moveOrMakeCopy($_POST['items'], $copyParams);
        }

        $this->render('copy');
    }

    /**
     * Создание копии позиций катлога
     */
    public function actionDirectMakeCopyOrMove()
    {
        print_r($_REQUEST['itemId']);
        print_r($_REQUEST['catIds']);
        return;
        if (isset($_POST['makeItemsCopy']) && isset($_POST['items'])) {

            $mode = (isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'copy');

            $copyParams = [
                'mode' => $mode,
                //                'catOfOriginal'=>$_POST['mode']=='catOfOriginal'
            ];

            $this->moveOrMakeCopy($_POST['items'], $copyParams);
        }

        $this->render('copy');
    }


    public function actionMassItemsToCategoriesConnect()
    {

        $this->connectItemToCats($_REQUEST['itemId'], $_REQUEST['catIds']);
    }

    public function actionMoveCat()
    {
        $params = json_decode(file_get_contents('php://input'), true);


        $draggedId = $params['dragged']['id'];
        $targetdId = $params['target']['id'];
        $moveType = $params['type'];

        CatCategory::moveTo($draggedId, $targetdId, $moveType);


        $this->actionEchoJsonCategories();
    }

    public function actionEchoJsonCategories()
    {
        $model = CatCategory::model();
        $model->loadCategories();
        $tmp = $model->categories;
        echo json_encode($tmp);
    }



    /**
     * Функция для переноса-копирования позиций каталога.
     *
     * @param $items
     * @param $options
     */
    function moveOrMakeCopy($items, $options)
    {


        $defaultOptions = [
            'categoriesIds' => null,
            'mode' => 'copy'/* move | copy | connect */,
            'catOfOriginal' => false
        ];

        $options = CMap::mergeArray($defaultOptions, $options);

        if (count($items)) {
            foreach ($items as $itemId) {
                $itemOriginal = CatItem::model()->findByPk($itemId);

                if ($itemOriginal) {

                    $itemCopy = new CatItem();
                    $table = $itemOriginal->getMetaData()->tableSchema;

                    $columns = $table->columns;
                    foreach ($columns as $column) {

                        $columnName = $column->name;

                        if ($columnName == $table->primaryKey) continue;
                        if ($columnName == 'published') {
                            $itemCopy->$columnName = 0;
                            continue;
                        };
                        $itemCopy->$columnName = $itemOriginal->$columnName;
                    }

                    $itemCopy->isNewRecord = true;
                    $itemCopy->insert();

                    $lastId = Yii::app()->db->getLastInsertId();

                    //Копируем изображения

                    Yii::import('pictureBox.components.PBox');
                    $PBox = new PBox('catalogItem', $itemId);
                    $PBox->copyToAnotherId($lastId);



                    //Копируем привязки к разделам, если нужно
                    if (isset($options['catOfOriginal']) && $options['catOfOriginal']) {
                        $CatItemsRelations = CatItemsToCat::model()->findAll('itemId = ' . $itemId);

                        foreach ($CatItemsRelations as $CatItemsToCat) {
                            $newCatItemToCat = new CatItemsToCat();
                            $newCatItemToCat->catId = $CatItemsToCat->catId;
                            $newCatItemToCat->itemId = $lastId;
                            $newCatItemToCat->save();
                        }
                    }

                    //Если есть массив ID разделов, то копируем карточку в каждый раздел
                    if (isset($options['categoriesIds'])) {

                        $this->connectItemToCats($lastId, $options['categoriesIds']);
                    }
                }
            }
        }
    }

    private function connectItemToCats($itemId, $catsIds)
    {

        if (is_array($catsIds) && count($catsIds) > 0) {

            foreach ($catsIds as $catId) {
                $newCatItemToCat = new CatItemsToCat();
                $newCatItemToCat->catId = $catId;
                $newCatItemToCat->itemId = $itemId;
                $newCatItemToCat->save();
            }
        }
    }
    public function actionAjaxCategoryCreate()
    {
        $rawData = file_get_contents("php://input");

        $attributes = CJSON::decode($rawData, true);

        if (!isset($attributes['pid'])) {
            $attributes['pid'] = -1;
        }

        $_POST['CatCategory'] = $attributes;
        $this->actionCreate();
    }
    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {

        $model = new CatCategory;


        if (isset($_POST['CatCategory'])) {
            $model->attributes = $_POST['CatCategory'];
            if ($model->save()) {
                if ($model->pid != -1) {
                    CatCategory::moveTo($model->id, $model->pid, 'middle');
                }
                $this->redirect(array('view', 'id' => $model->id));
            } else  throw new Exception('Не удалось создать категорию каталога');
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
    public function actionDelete($id, $withGoods = false)
    {
        /** @var CatCategory $catModel */
        $catModel = $this->loadModel($id);
        $catChilds = $catModel->getAllCatChilds();
        if ($withGoods) {
            $catChilds[] = ['id' => $id];
            foreach ($catChilds as $catChild) {
                $catId = $catChild['id'];
                $catItemsTocatModels = CatItemsToCat::model()->findAllByAttributes([
                    'catId' => $catId
                ]);

                foreach ($catItemsTocatModels as $catItemsTocatModel) {
                    if (!is_null($catItemsTocatModel->item)) {
                        if (!$catItemsTocatModel->item->delete()) {
                            throw new CHttpException(400, 'Не удалось удалить элемент каталога');
                            return;
                        }
                    } else {
                        $catItemsTocatModel->delete();
                    }
                }
            }
        }




        foreach ($catChilds as $catChild) {
            $idForDelete = $catChild['id'];
            if (!CatCategory::model()->findByPk($idForDelete)->delete()) {
                throw new CHttpException(400, 'Не удалось удалить подкатегорию ' . $idForDelete);
                return;
            }
        }
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

        //        $model = new CatCategory('search');
        //        $model->unsetAttributes(); // clear any default values
        //        if (isset($_GET['CatCategory']))
        //            $model->attributes = $_GET['CatCategory'];
        //
        //        $this->render('admin', array(
        //            'model' => $model,
        //            'pid' => $pid
        //        ));
        $this->render('catManage');
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

    //    public function actionOrderUp($id)
    //    {
    //        $model = $this->loadModel($id);
    //        $orderModel = $model->getCategory($id);
    //
    //        $this->groupId = $orderModel['pid'];
    //        $this->orderUp($id);
    //    }

    //    public function actionOrderDown($id)
    //    {
    //        $model = $this->loadModel($id);
    //        $orderModel = $model->getCategory($id);
    //        $this->groupId = $orderModel['pid'];
    //        $this->orderDown($id);
    //    }

    public function actionTidyPost($id)
    {

        $model = $this->loadModel($id);

        Yii::import('application.modules.pictureBox.components.PBox');

        $pbox = new PBox('catalogCategory', $id);

        $images = $pbox->pictures;

        $text = $model->text;

        Yii::import('application.modules.begemot.components.tidy.TidyBuilder');

        $this->module->tidyleadImage != 0 ? $leadImage = 1 : $leadImage = 0;

        $tidy = new TidyBuilder($model->text, $this->module->tidyConfig, $images, $leadImage);

        $model->text = $tidy->renderText();

        $model->save();

        $this->redirect(array('/catalog/catCategory/update', 'id' => $model->id,));
    }
}