<?php


class CatItemOptionsController extends Controller
{

    public function init() {
        parent::init();
        // Ваш код инициализации
        $path = Yii::getPathOfAlias('catalog.views.catItem.commonMenu');
        $this->menu = require $path.'.php';
    }
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

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => [
                    'ajaxRenderTableOptionRow',
                    'ajaxNewOptionFor',
                    'ajaxRemoveOptionFrom',
                    'ajaxChangeIsBaseState',
                    'layoutOptionsTable',
                    'ajaxUpdateOptionRelation',
                    'ajaxNewOptionOrder',
                    'makeImport',
                    'removeOptions','manage'
                ],

                'expression' => 'Yii::app()->user->canDo("Catalog")'
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionAjaxRenderTableOptionRow($itemId, $optionForThisId)
    {


        if (Yii::app()->request->isAjaxRequest) {

            $item = CatItem::model()->with('options')->findByPk($itemId);

            $catItemToItem = CatItemsToItems::model()->findByAttributes(['itemId' => $optionForThisId, 'toItemId' => $itemId]);


            $tableLineViewData = [

                'originalImage' => $item->getItemMainPicture('original'),
                'adminImage' => $item->getItemMainPicture('admin'),
                'name' => $item->name,
                'itemId' => $item->id,
                'isBase' => $catItemToItem->isBase,
                'options' => $item->options
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

    public function actionAjaxNewOptionOrder($itemId)
    {

        $sortCollection = $_REQUEST['sortCollection'];

        foreach ($sortCollection as $order => $optionId) {
            $itemToItem = CatItemsToItems::model()->findByAttributes(['itemId' => $itemId, 'toItemId' => $optionId]);
            $itemToItem->order = $order;

            if (!$itemToItem->save()) {
                throw new Exception("Ошибка сохранения модели");
            }
        }
    }

    public function actionAjaxUpdateOptionRelation($baseItemId, $childrenOptionId, $action)
    {
        if ($action == 'connect') {
            $catItemsToItems = new CatItemsToItems();
            $catItemsToItems->itemId = $baseItemId;
            $catItemsToItems->toItemId = $childrenOptionId;

            if ($_REQUEST['target'] == 'relation')
                $catItemsToItems->cantWorkWithOut = 1;

            if ($_REQUEST['target'] == 'conflict')
                $catItemsToItems->conflict   = 1;

            $catItemsToItems->save();
        }

        if ($action == 'disconnect') {
            $deselected = CatItemsToItems::model()->findByAttributes([
                'itemId' => $baseItemId,
                'toItemId' => $childrenOptionId
            ]);

            if (!is_null($deselected)) {

                $deselected->delete();
            }
        }

        return;
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
                print_r($deselected);
                if (!is_null($deselected)) {

                    if ($deselected->delete()) {
                        echo 'deleted';
                    } else {
                        echo 'not deleted';
                    };
                } else {
                }
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
            $searchParams = ['itemId' => $selectedOptionId];
            if ($_REQUEST['target'] == 'relation') {
                $searchParams['cantWorkWithOut'] = 1;
            }
            if ($_REQUEST['target'] == 'conflict') {
                $searchParams['conflict'] = 1;
            }
            $alreadyConnetedOptions = CatItemsToItems::model()->findAllByAttributes($searchParams);

            $this->renderPartial('/catItem/optionsAsTable', ['paentItemId' => $paentItemId, 'alreadyConnetedOptions' => $alreadyConnetedOptions]);
        }
    }

    public function actionMakeImport($id)
    {

        $catItem = CatItem::model()->findByPk($id);
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $options = $request->options;


        foreach ($options as $option) {
            $searchOption = CatItemsToItems::model()->findByAttributes(['itemId' => $id, 'toItemId' => $option->id]);
            if (!$searchOption) {
                $optionRelation = new CatItemsToItems();
                $optionRelation->itemId = $id;
                $optionRelation->toItemId = $option->id;
                $optionRelation->order = $option->order;
                $optionRelation->save();
            } else {
                echo 'уже есть!';
                $searchOption->itemId = $id;
                $searchOption->toItemId = $option->id;
                $searchOption->order = $option->order;
                $searchOption->save();
            }
        }
    }

    public function actionRemoveOptions($id)
    {

        $catItem = CatItem::model()->findByPk($id);
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $options = $request->options;
        //получили опции которые нужно убрать из позиции с переданным $id

        foreach ($options as $option) {
            $searchOption = CatItemsToItems::model()->findByAttributes(['itemId' => $id, 'toItemId' => $option->id]);
            if ($searchOption) {
                $searchOption->delete();
                echo 'Открепляем опцию: ' . $searchOption->toItem->name;
            }
        }
    }

    public function actionManage()
    {
        $this->layout = 'begemot.views.layouts.bs5clearLayout';
        $this->render('manage');
    }
}
