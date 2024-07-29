<?php

Yii::import('webroot.protected.jobs.*');

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
            'ajaxOnly + delete', // we only allow deletion via POST request
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


                'actions' => array(
                    'delete', 'createColor', 'deleteColor', 'setColor',
                    'setColorTo', 'unsetColorTo', 'ajaxCreate',
                    'deleteModifFromItem','MassImages',
                    'create', 'update', 'togglePublished', 'toggleTop', 'index', 'view', 'deleteItemToCat', 'tidyItemText', 'getItemsFromCategory', 'options', 'test'
                ),


                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array(
                'deny',  // deny all users
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


        //        if (isset($_POST['CatItem'])) {

        $model->name = "Новая позиция";
        if ($model->save()) {

            $this->redirect(array('catItem/update', 'id' => $model->id));
        } else {
            throw new Exception(json_encode($model->getErrors()), 1);
        }

        //        }

        //        if (!isset($_POST['returnId'])) {
        //            $this->render('create', array(
        //                'model' => $model,
        //            ));
        //        }

    }
    public function actionAjaxCreate()
    {
        $model = new CatItem;
        $model->attributes = $_POST['CatItem'];
        if ($model->save()) {

            echo $model->id;
        } else {
            echo 'ошибка сохранения';
        }
    }
    public function actionGetItemsFromCategory($catId, $curCatId)
    {
        $model = CatItemsToCat::model()->with('item')->findAll(array('condition' => 't.catId=' . $catId, 'order' => 't.order ASC'));

        $currentPosition = 1;
        $flag = true;
        $array = array('html' => '', 'ids' => array(), 'currentPos' => '');
        foreach ($model as $cat) {
            if ($curCatId != $cat->item->id) {
                $array['html'] .= "<option value='" . $cat->item->id . "'>" . $cat->item->name . "- (" . $cat->item->id . ")</option>";
                $array['ids'][] = $cat->item->id;

                if ($flag) {
                    $currentPosition++;
                }
            } else {
                $flag = false;
            }
        }

        $array['currentPos'] = $currentPosition;

        if (ob_get_contents())
            ob_end_clean();

        echo json_encode($array);
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


        if (Yii::app()->request->isAjaxRequest) {

            if (isset($_GET['changePrice'])) {
                $model->price = $_GET['changePrice'];
                $model->save();
            }
            return;
        }

        $message = '';


        if (isset($_GET['setMainCat'])) {
            $model->catId = $_GET['setMainCat'];
            $model->save();
        }



        // change positions
        if (isset($_POST['changePosition'])) {


            $category = $_POST['categoryId'];
            $item = $_POST['item'];
            $currentItem = $_POST['currentItem'];

            if (!empty($_POST['itemId'])) {
                $item = $_POST['itemId'];
            }


            if (isset($_POST['pasteOnFirstPosition'])) {
                $itemModel = $itemModel = CatItemsToCat::model()->with('item')->find(array('condition' => 't.catId=' . $category, 'order' => 't.order ASC'));
            } else if (isset($_POST['pasteOnLastPosition'])) {
                $itemModel = $itemModel = CatItemsToCat::model()->with('item')->find(array('condition' => 't.catId=' . $category, 'order' => 't.order DESC'));
            } else {
                $itemModel = CatItemsToCat::model()->with('item')->find(array('condition' => 't.catId=' . $category . " AND t.itemId=" . $item));
            }


            $currentItemModel = CatItemsToCat::model()->with('item')->find(array('condition' => 't.catId=' . $category . " AND t.itemId=" . $currentItem));

            if ($currentItemModel->catId != 0 && $itemModel->catId != 0) {

                $itemsToChange = CatItemsToCat::model()->with('item')->findAll(array('condition' => 't.order >=' . $itemModel->order, 'order' => 't.order ASC'));


                if (isset($_POST['pasteOnLastPosition'])) {
                    $order = $itemModel->order - 1;
                } else {
                    $order = $itemModel->order + 1;
                }


                foreach ($itemsToChange as $item) {
                    $item->order = $order;
                    $item->save();

                    if (isset($_POST['pasteOnLastPosition'])) {
                        $order--;
                    } else {
                        $order++;
                    }
                }

                $currentItemModel->order = $itemModel->order;
                $currentItemModel->save();

                $message = "Сохранено";
            }
        }
        // --change positions


        if (isset($_POST['saveModif'])) {
            if (isset($_POST['modif'])) {
                foreach ($_POST['modif'] as $itemId) {
                    $item = CatItem::model()->findByPk($itemId);
                    $item->modOfThis = $id;
                    $item->save();
                    $this->redirect('/catalog/catItem/update/id/' . $id . '/tab/modifications');
                }
            }
        }

        if (isset($_POST['saveItemsToItems'])) {
            CatItemsToItems::model()->deleteAll(array("condition" => 'itemId=' . $id . " OR toItemId=" . $id));

            if (isset($_POST['options'])) {
                foreach ($_POST['options'] as $itemId) {
                    $item = new CatItemsToItems();

                    $item->itemId = $id;
                    $item->toItemId = $itemId;

                    $item->save();
                }
            }

            if (isset($_POST['items'])) {
                foreach ($_POST['items'] as $itemId) {
                    $item = new CatItemsToItems();

                    $item->itemId = $itemId;
                    $item->toItemId = $id;

                    $item->save();
                }
            }
        }


        $fileListOfDirectory = array();
        $synched = false;
        if (isset(Yii::app()->modules['parsers'])) {


            Yii::import('application.modules.parsers.models.ParsersLinking');
            Yii::import('application.modules.parsers.models.ParsersStock');

            $synched = ParsersLinking::model()->with('item')->find(array('condition' => "t.toId='" . $model->id . "'"));


            $fileListOfDirectory = array();
            if (!$synched) {

                $fileListOfDirectory = array();


                if (is_dir(Yii::app()->basePath . '/jobs')) {
                    foreach (glob(Yii::app()->basePath . '/jobs/*ParserJob.php') as $path) {

                        $className = basename($path);
                        $className = str_replace('.php', '', $className);
                        $class = new $className;

                        array_push($fileListOfDirectory, array('name' => $class->getName(), 'className' => $className));
                    }
                }
            }
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
            $this->redirect(array('update', 'id' => $model->id, 'tab' => $tab));
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
        // Удаляем записи из catItemsToCat

        $model->delete();





        //        //Удаляем привязки к категориям
        //        $ParsersLinkingRelations = ParsersLinking::model()->findAll('toId = ' . $id);
        //
        //        foreach ($ParsersLinkingRelations as $parsersLinking) {
        //
        //
        //            $parsersStock = $parsersLinking->linking;
        //            $parsersStock->linked = 0;
        //            $parsersStock->save();
        //
        //            $parsersLinking->delete();
        //        }


        if (!Yii::app()->request->isAjaxRequest)
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {

        //        $dataProvider = new CActiveDataProvider('CatItem',
        //            array(
        //                'criteria' => array('order' => '`id` desc'),
        //                'pagination' => array(
        //                    'pageSize' => 1000,
        //                ),
        //            ));

        $model = new CatItem('search');


        if (isset($_GET['CatItem']))
            $model->Attributes = $_GET['CatItem'];


        $this->render('index', array(
            'model' => $model,

        ));
    }

    public function actionTogglePublished($id)
    {
        $model = CatItem::model()->findByPk($id);

        $model->published = ($model->published) ? 0 : 1;

        if ($model->save()) {
            echo "saved";
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }


    public function actionToggleTop($id)
    {
        $model = CatItem::model()->findByPk($id);

        $model->top = ($model->top) ? 0 : 1;

        if ($model->save()) {
            echo "saved";
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }


    public function actionDeleteModifFromItem($itemId)
    {

        if (Yii::app()->request->isAjaxRequest) {

            $item = CatItem::model()->findByPk($itemId);
            print_r($item->modOfThis);
            $item->modOfThis = null;

            if (!$item->save()) {
                echo 'Ошибка!';
            }
        }
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

    public function actionDeleteColor($colorId)
    {
        if (Yii::app()->request->isAjaxRequest) {

            $color = CatColor::model()->findByPk($colorId);
            $color->delete();
            CatColorToCatItem::model()->deleteAllByAttributes(['colorId' => $colorId]);

            return true;
        }
    }

    public function actionSetColor($colorId, $colorCode)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $color = CatColor::model()->findByPk($colorId);
            $color->colorCode = $colorCode;
            $color->save();

            return true;
        }
    }

    public function actionCreateColor($colorName, $colorCode, $catItemId)
    {

        CatColor::createColor($colorName, $colorCode, $catItemId);

        $this->redirect('/catalog/catItem/update/id/' . $catItemId . '/tab/colors');
    }

    public function actionSetColorTo($colorId, $catItemId)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $color = new CatColorToCatItem();
            $color->catItemId = $catItemId;
            $color->colorId = $colorId;
            $color->save();

            return true;
        }
    }

    public function actionUnsetColorTo($colorId, $catItemId)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $color = CatColorToCatItem::model()->findByAttributes(['colorId' => $colorId, 'catItemId' => $catItemId]);
            $color->delete();

            return true;
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

        $tidy = new TidyBuilder($model->text, $this->module->tidyConfig, $images, $leadImage);

        $model->text = $tidy->renderText();

        $model->save();

        $this->redirect(array('/catalog/catItem/update', 'id' => $model->id,));
    }

    public function actionMassImages(){
        $this->layout = 'begemot.views.layouts.bs5clearLayout';
        $this->render('manageImages');
    }

}
