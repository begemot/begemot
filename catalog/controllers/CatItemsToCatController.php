<?php

class CatItemsToCatController extends Controller
{
    public $layout = 'begemot.views.layouts.column2';


    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
            'ajaxOnly + changeThroughDisplayValue',
        );
    }

    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('delete', 'orderUp', 'orderDown', 'changeThroughDisplayValue',
                    // 'index',
                    'admin'),

                'expression' => 'Yii::app()->user->canDo("Catalog")'

            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));

    }

    public function behaviors()
    {
        return array(
            'CBOrderControllerBehavior' => array(
                'class' => 'begemot.extensions.order.BBehavior.CBOrderControllerBehavior',
                'groupName' => 'catId'
            )
        );
    }

    public function actionOrderUp($id)
    {
        $model = $this->loadModel($id);

        $this->groupId = $model->catId;
        $this->orderUp($id);

    }

    public function actionOrderDown($id)
    {
        $model = $this->loadModel($id);


        $this->groupId = $model->catId;
        $this->orderDown($id);
    }

    public function actionAdmin($id)
    {

        $cat = CatCategory::model()->findByPk($id);

        if ($cat->type == 'base') {
            $model = new CatItemsToCat('search');
            $model->unsetAttributes();
            $model->dbCriteria->order = "`t`.`order` ASC";

            if (isset($_GET['CatItemsToCat']))
                $model->attributes = $_GET['CatItemsToCat'];

            $this->render('admin', array(
                'category' => CatCategory::model()->findByPk($id),
                'id' => $id,
                'model' => $model,
            ));
        } else {
            Yii::import('schema.models.*');
            Yii::import('schema.components.*');
            Yii::import('system.web.CArrayHelper');
            /** @var SchmGroup $schemaGroup */

            $schemaGroup = new SchmGroup();
            $groupedArray = $schemaGroup->getGroupData($id, 'catItem',array(1, 3));
            print_r($groupedArray);
//            $filteredData = CArrayHelper::filter($groupedArray, array('groupId' => 4));
//            // Create a new data provider object with the array of data
//            $dataProvider = new CArrayDataProvider($filteredData);
//// Create the sort object and set the sorting criteria
//            $sort = new CSort();
//            $sort->attributes = [
//                'groupId'
//            ];
//            $sort->defaultOrder = '1 DESC';
//// Pass the sort object to the data provider's sort property
//            $dataProvider->setSort($sort);
//
//
//            $dataProvider->filter = array('groupId' => 4);
//
//
//            $sortedData = $dataProvider->getData();
//            print_r($sortedData);
            // Set the sort criteria for the data provider



        }


    }

    public function loadModel($id)
    {
        $model = CatItemsToCat::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function actionChangeThroughDisplayValue($cat_id, $item_id, $value)
    {

        $model = CatItemsToCat::model()->find(array(
            'condition' => 'catId = :cat AND itemId = :item',
            'params' => array(':cat' => $cat_id, ':item' => $item_id)
        ));

        $parent_id = CatCategory::model()->findByPk($cat_id)->pid;
        //$root_id = CatCategory::model()->findByPk($parent_id)->pid;
        $table = CatItemsToCat::model()->tableName();
        $maxOrderValue = (Yii::app()->db->createCommand()
                ->select('max(`order`) as max')
                ->from($table)
                ->queryScalar()) + 1;

        while (true) {

            $attr = [
                'itemId' => $item_id,
                'catId' => $parent_id
            ];
            $throughCatItemToCat = CatItemsToCat::model()->findByAttributes($attr);
            echo $item_id . ' ' . $parent_id;
            // CHECKED
            if ($value == 1) {

                if (is_null($throughCatItemToCat)) {

                    $sql = "INSERT INTO $table (itemId, catId, `order`, is_through_display_child,through_display_count) VALUES (:itemId, :catId, :order, 1,1)";
                    $parameters = array(":itemId" => $item_id, ":catId" => $parent_id, ":order" => $maxOrderValue);
                    Yii::app()->db->createCommand($sql)->execute($parameters);

                    $maxOrderValue++;
                } else {
                    echo 'увеличиваем количество мнимых карточек
                    ';
                    $throughCatItemToCat->through_display_count++;
                    $throughCatItemToCat->save();
                }

                if (CatCategory::model()->findByPk($parent_id)->pid == -1) {
                    break;
                }

            } else {

                $cat_level = CatCategory::model()->findByPk($parent_id)->pid;
                // UNCHECKED
                $itemsToCat = CatItemsToCat::model()->find(array(
                    'condition' => 'itemId = :itemId AND catId = :catId',
                    'params' => array(
                        ':itemId' => $item_id,
                        ':catId' => $parent_id
                    )
                ));

                if ($itemsToCat->through_display_count == 1) {
                    $itemsToCat->delete();
                } else {
                    $itemsToCat->through_display_count--;
                    $itemsToCat->save();
                }

                if ($cat_level == -1) {
                    break;
                }
            }


            $parent_id = CatCategory::model()->findByPk($parent_id)->pid;
        }

        $model->through_display = $value;
        $model->save();
    }

}
