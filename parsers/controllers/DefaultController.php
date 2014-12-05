<?php

class DefaultController extends Controller
{
    public $layout='begemot.views.layouts.column2';
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
                'actions'=>array('linking', 'create','update','index','view', 'do', 'syncCard', 'updateCard', 'doChecked', 'deleteLinking', 'parseChecked', 'parseNew', 'getParsedForCatItem', 'cron'),
                'expression'=>'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionParseChecked()
    {
        $files = $_GET['parse'];
        $finded = ParsersLinking::model()->findAllByAttributes(array('filename'=>$files));
        $items = array();

        foreach ($finded as $item) {
            if ($item->linking->price != $item->item->price || $item->linking->quantity != $item->item->quantity) {
                $items[] = $item;
            }
        }
        $this->render('parseChecked',array(
            'items' => $items,
        ));

    }

    public function actionSyncCard()
    {
        $model = new ParsersLinking;

        $return = array('code'=> true, 'toUpdate' => '', 'toAllLinks' => '');

        if(isset($_POST['ParsersLinking']))
        {
            $model->attributes=$_POST['ParsersLinking'];

            if($model->save()){

                $stockModel = ParsersStock::model()->findByPk($_POST['ParsersLinking']['fromId']);
                $stockModel->linked = 1;
                $stockModel->save();

                if ($model->linking->price != $model->item->price || $model->linking->quantity != $model->item->quantity) {
                    $return['toUpdate'] = $this->renderPartial('oneItem',array(
                        'item'=>$model,
                    ), true);
                }


                $return['toAllLinks'] = $this->renderPartial('oneItemLinking',array(
                    'item'=>$model,
                ), true);
            }

        }


        ob_clean();
        echo json_encode($return);


    }

    public function actionGetParsedForCatItem($itemId, $file)
    {
        $catItems = CatItem::model()->findByPk($itemId);

        $this->renderPartial('listForCatItems',array(

            'itemList' => ParsersStock::model()->findAllByAttributes(array('filename' => $file, 'linked' => 0)),
            'itemId' => $itemId
        ));
    }

    public function actionUpdateCard()
    {

        if (isset($_POST['id'])) {

            $model = CatItem::model()->findByPk($_POST['id']);
            $model->price = $_POST['price'];
            $model->quantity = $_POST['quantity'];

            ob_clean();
            if ($model->save()) {
                echo "1";
            }
            else{
                echo "0";
            }
        }
    }

    public function actionDoChecked()
    {

        if(isset($_POST['item'])){
            foreach ($_POST['item'] as $item) {
                if (isset($item['id'])) {
                    $model = CatItem::model()->findByPk($item['id']);
                    $model->price = $item['price'];
                    $model->quantity = $item['quantity'];
                    $model->save();
                }
            }
        }

        $this->redirect($_POST['url']);
    }

    public function actionParseNew($className)
    {

        $class = new $className;
        $class->runJob();
        $json = $class->getLastParsedData();


        ParsersStock::model()->deleteAll(array('condition' => "`filename`='" . $class->getName() . "'"));

        $length = count($json->items);

        

        foreach ($json->items as $item) {
            $new = new ParsersStock;
            $item = (array)$item;
            $item['filename'] = $json->name;
            $item['name'] = substr($item['name'], 0, 99);

            if (ParsersLinking::model()->find(array(
                'condition'=>'fromId=:fromId',
                'params'=>array(':fromId'=>$item['id'])))
            ) {
                $item['linked'] = 1;
            }

            $new->attributes = $item;

            $new->save();
        }
        ob_clean();

        $tempfile = file_get_contents(Yii::app()->basePath.'/../files/parsersData/time.txt');
        $timeArray = json_decode($tempfile);
        $timeArray = (array)$timeArray;


        echo date("d.m.Y H:i", $timeArray[$class->getName()]);


    }

    public function actionDo($file)
    {

        $parserLinkingIds = ParsersLinking::model()->findAll();

        $iDsArray = array();
        foreach ($parserLinkingIds as $parserData) {
            $iDsArray[] = $parserData['toId'];
        }

        $criteria = new CDbCriteria();
        $criteria->addNotInCondition("id", $iDsArray);
        $criteria->order = 'name ASC';

        $catItems = CatItem::model()->findAll($criteria);


        $itemList = array(
            'combined' => ParsersLinking::model()->findAllByAttributes(array('filename' => $file), array('order' => 'id ASC')),
            'combinedAndChanged' => array(),
            'notCombined' => ParsersStock::model()->findAllByAttributes(array('filename' => $file, 'linked' => 0), array('order' => 'id ASC')),
        );

        if ($itemList['combined']) {
            foreach ($itemList['combined'] as $item) {
                if ($item->linking->price != $item->item->price || $item->linking->quantity != $item->item->quantity) {
                    $itemList['combinedAndChanged'][] = $item;
                }
            }

        }



        $this->render('do',array(
            // 'models'=>$models,
            // 'return' => $return,
            'filename' => $file,
            'itemList' => $itemList,
            'allItems' => $catItems
        ));

    }

    public function actionLinking()
    {

        $model = ParsersLinking::model()->findAll(array('order' => 'id DESC'));

        $this->render('linking',array(

            'items' => $model
        ));
    }

    public function actionIndex()
    {
        $this->render('index',array(
            // 'models'=>$models,
            // 'return' => $return,
            'fileListOfDirectory' => $this->getFiles()
        ));

    }

    public function getFiles()
    {
        $fileListOfDirectory = array();
        $timeArray = array();
        if (file_exists(Yii::app()->basePath.'/../files/parsersData/time.txt') ) {
            $tempfile = file_get_contents(Yii::app()->basePath.'/../files/parsersData/time.txt');
            $timeArray = json_decode($tempfile);
            $timeArray = (array)$timeArray;
        }

        foreach(glob(Yii::app()->basePath.'/jobs/*ParserJob.php') as $path) {  

            $time = 0;

            $className = basename($path);
            $className = str_replace('.php', '', $className);
            $class = new $className;

            if (array_key_exists($class->getName(), $timeArray)) {
                $time = $timeArray[$class->getName()];
            } else{
                $time = 1;
            }

            array_push ( $fileListOfDirectory, array('name' => $class->getName(), 'time' => $time, 'className' => $className) );
        }
        


        return $fileListOfDirectory;
    }

    public function actionDeleteLinking($id)
    {
        $model=ParsersLinking::model()->findByPk($id);

        $stockModel = ParsersStock::model()->findByPk($model->fromId);
        $stockModel->linked = 0;
        $stockModel->save();


        ob_clean();
        if ($model->delete()) {
            echo "1";
        }
        else{
            echo "0";
        }

    }

}
