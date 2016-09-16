<?php


class CatItemOptionsController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
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

                'actions' => ['ajaxRenderTableOptionRow', 'ajaxNewOptionFor', 'ajaxRemoveOptionFrom', 'ajaxChangeIsBaseState', 'layoutOptionsTable', 'ajaxUpdateOptionRelation'],

                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionAjaxRenderTableOptionRow($itemId, $optionForThisId)
    {


        if (Yii::app()->request->isAjaxRequest) {

            $item = CatItem::model()->findByPk($itemId);

            $catItemToItem = CatItemsToItems::model()->findByAttributes(['itemId' => $optionForThisId, 'toItemId' => $itemId]);


            $tableLineViewData = [
                'originalImage' => $item->getItemMainPicture('original'),
                'adminImage' => $item->getItemMainPicture('admin'),
                'name' => $item->name,
                'itemId' => $item->id,
                'isBase' => $catItemToItem->isBase
            ];


            $this->renderPartial('/catItem/tableLine', $tableLineViewData);
        }
    }

    public function actionAjaxNewOptionFor($itemId, $optionId)
    {
        $catItemsToItems = new CatItemsToItems();
        $catItemsToItems->itemId = $itemId;
        $catItemsToItems->toItemId = $optionId;
        $catItemsToItems->save();
    }

    public function actionAjaxUpdateOptionRelation($itemId)
    {
        if (isset($_REQUEST['optionIdArray'])) {
            $selectedItemsId = $_REQUEST['optionIdArray'];
            foreach ($selectedItemsId as $selectedId) {
                $catItemsToItems = new CatItemsToItems();
                $catItemsToItems->itemId = $itemId;
                $catItemsToItems->toItemId = $selectedId;
                $catItemsToItems->cantWorkWithOut = 1;
                $catItemsToItems->save();
            }
        }

        if (isset($_REQUEST['deselectedOptions'])) {

            $deselectedItemsId = $_REQUEST['deselectedOptions'];
            foreach ($deselectedItemsId as $deselectedId) {
                $deselected = CatItemsToItems::model()->findByAttributes([
                    'itemId' => $itemId,
                    'toItemId' => $deselectedId
                ]);

                if (!is_null($deselected)) $deselected->delete();

            }
        }
    }

    public function actionAjaxRemoveOptionFrom($itemId, $optionId)
    {
        $attr = [
            'itemId' => $itemId,
            'toItemId' => $optionId
        ];
        $catItemsToItems = CatItemsToItems::model()->findByAttributes($attr);
        $catItemsToItems->delete();
    }

    public function actionAjaxChangeIsBaseState($itemId, $optionId)
    {
        $attr = [
            'itemId' => $itemId,
            'toItemId' => $optionId
        ];
        $catItemsToItems = CatItemsToItems::model()->findByAttributes($attr);

        if ($catItemsToItems->isBase == true) {
            $catItemsToItems->isBase = false;
        } else {
            $catItemsToItems->isBase = true;
        }

        $catItemsToItems->save();
    }

    public function actionLayoutOptionsTable($paentItemId = null, $selectedOptionId = null)
    {
        if (Yii::app()->request->isAjaxRequest) {

            $alreadyConnetedOptions = CatItemsToItems::model()->findAllByAttributes([
                'itemId' => $selectedOptionId
            ]);

            $this->renderPartial('/catItem/optionsAsTable', ['paentItemId' => $paentItemId, 'alreadyConnetedOptions' => $alreadyConnetedOptions]);
        }
    }
}