<?php

class DefaultController extends Controller
{
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

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('linking', 'ignoreImage', 'updateOtherFields', 'updateCategories', 'updateOptions', 'updateModifs', 'create', 'update', 'index', 'view', 'do', 'syncCard', 'updateCard', 'doChecked', 'deleteLinking', 'parseChecked', 'parseNew', 'getParsedForCatItem', 'cron'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIgnoreImage()
    {

        if (isset($_POST['md5']) & isset($_POST['sha1']) & isset($_POST['image'])) {
            $md5 = $_POST['md5'];
            $sha1 = $_POST['sha1'];
            $image = $_POST['image'];

            $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/ignoredImages';

            if (!file_exists($dir))
                mkdir($dir, 0777);

            $file = $image;
            $temp = explode('.', $file);
            $imageExt = end($temp);

            $filePath = $dir . "/" . $md5 . '_' . $sha1 . '.' . $imageExt;

            copy($image, $filePath);

            $model = new ParsersIgnoreImages;
            $model->md5 = $md5;
            $model->sha1 = $sha1;
            $model->image = $filePath;

            if ($model->save()) {
                echo "1";
            } else {
                throw new Exception('errors', 1);

            }
        } else throw new Exception("Присланы не все параметры", 1);

    }


    public function actionParseChecked()
    {
        $files = $_GET['parse'];
        $finded = ParsersLinking::model()->findAllByAttributes(array('filename' => $files));
        $items = array();

        foreach ($finded as $item) {
            if ($item->linking->price != $item->item->price || $item->linking->quantity != $item->item->quantity) {
                $items[] = $item;
            }
        }
        $this->render('parseChecked', array(
            'items' => $items,
        ));

    }

    public function actionSyncCard()
    {
        $model = new ParsersLinking;

        $return = array('code' => true, 'toUpdate' => '', 'toAllLinks' => '');

        if (isset($_POST['ParsersLinking'])) {
            $model->attributes = $_POST['ParsersLinking'];

            if ($model->save()) {

                $stockModel = ParsersStock::model()->findByPk($_POST['ParsersLinking']['fromId']);
                $stockModel->linked = 1;
                $stockModel->save();

                if ($model->linking->price != $model->item->price || $model->linking->quantity != $model->item->quantity) {
                    $return['toUpdate'] = $this->renderPartial('oneItem', array(
                        'item' => $model,
                    ), true);
                }


                $return['toAllLinks'] = $this->renderPartial('oneItemLinking', array(
                    'item' => $model,
                ), true);
            }

        }


        ob_clean();
        echo json_encode($return);


    }


    public function actionUpdateModifs()
    {

        echo $itemId = $_POST['id'];
        echo '
';
        if (isset($_POST['modifs'])) {

            $_POST['modifs'] = json_decode($_POST['modifs']);

            echo $modif = $_POST['modifs'];
            echo '
';


            echo $catModifId = $itemId;
            echo '
';
            if ($modifOwner = ParsersLinking::model()->find(array(
                    'condition' => 'fromId=:fromId',
                    'params' => array(':fromId' => $modif))
            )
            ) {
                echo $catModifOwner = $modifOwner->toId;
                echo '<br>';

                Yii::import('application.modules.catalog.models.CatItems');
                $catModifModel = CatItem::model()->findByPk($catModifId);
                $catModifModel->modOfThis = $catModifOwner;
                $catModifModel->save();
            }

        }


        $return = array('code' => true);


        //ob_clean();
        echo json_encode($return);


    }

    public function actionUpdateOtherFields()
    {
        $id = $_POST['id'];
        $attrArray = [
            'parsers_stock_id' => $id
        ];

        $otherFieldsModels = ParsersOtherFields::model()->findAllByAttributes($attrArray);

        if (is_array($otherFieldsModels) && count($otherFieldsModels) > 0) {


            if ($parserLinking = ParsersLinking::model()->find(array(
                    'condition' => 'fromId=:fromId',
                    'params' => array(':fromId' => $id))
            )
            ) {
                $catItemId = $parserLinking->toId;
                Yii::import('application.modules.catalog.models.CatItems');
                $model = CatItem::model()->findByPk($catItemId);
                $columnsArrray = $model->tableSchema->columnNames;
                $columnsArrray = array_flip($columnsArrray);
                echo 'id найденой модели CatItem - ' . $model->id;
                foreach ($otherFieldsModels as $field) {
                    $fieldName = $field->name;
                    if (isset($columnsArrray[$fieldName])) {
                        echo 'Совпало поле ' . $fieldName;
                        $model->$fieldName = $field->value;
                    }
                }
                if (!$model->save()) {
                    echo 'Ошибка осхранения модели';
                }

            }

        }
    }

    public function actionUpdateOptions()
    {

        $itemId = $_POST['id'];

        $_POST['parents'] = json_decode($_POST['parents']);

        if (isset($_POST['parents'])) {
            foreach ($_POST['parents'] as $parent) {

                if ($parentModel = ParsersLinking::model()->find(array(
                        'condition' => 'fromId=:fromId',
                        'params' => array(':fromId' => $parent))
                )
                ) {
                    $parentId = $parentModel->toId;

                    Yii::import('application.modules.catalog.models.CatItemsToItems');

                    $item = new CatItemsToItems();

                    $item->itemId = $parentId;
                    $item->toItemId = $itemId;

                    $item->save();
                }

            }

        } else throw new Exception("Нету родительских опций", 1);


        $return = array('code' => true);


        //ob_clean();
        echo json_encode($return);


    }

    public
    function actionUpdateCategories()
    {

        $itemId = $_POST['id'];

        $_POST['groups'] = json_decode($_POST['groups']);

        if (isset($_POST['groups'])) {
            foreach ($_POST['groups'] as $group_name) {

                if ($group_relation = ParsersCategoryConnection::model()->find(array(
                        'condition' => 'connect_name=:connect_name',
                        'params' => array(':connect_name' => $group_name))
                )
                ) {

                    Yii::import('application.modules.catalog.models.CatItemsToCat');

                    $catItemsToCat = new CatItemsToCat();

                    $catItemsToCat->itemId = $itemId;
                    $catItemsToCat->catId = $group_relation->category_id;

                    $catItemsToCat->save();

                    $item = CatItem::model()->findByPk($itemId);
                    if (isset($item->catId)) {
                        if ($item->catId == 0) {
                            $item->catId = $group_relation->category_id;

                            $item->save();
                        }
                    }

                }


            }

        } else throw new Exception("Нету категорий", 1);


        $return = array('code' => true);


        //ob_clean();
        echo json_encode($return);


    }

    public
    function actionGetParsedForCatItem($itemId, $file)
    {
        $catItems = CatItem::model()->findByPk($itemId);

        $this->renderPartial('listForCatItems', array(

            'itemList' => ParsersStock::model()->findAllByAttributes(array('filename' => $file, 'linked' => 0)),
            'itemId' => $itemId
        ));
    }

    public
    function actionUpdateCard()
    {

        if (isset($_POST['id'])) {

            $model = CatItem::model()->findByPk($_POST['id']);
            $model->price = $_POST['price'];
            $model->quantity = $_POST['quantity'];

            ob_clean();
            if ($model->save()) {
                echo "1";
            } else {
                echo "0";
            }
        }
    }

    public
    function actionDoChecked()
    {

        if (isset($_POST['item'])) {
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

    public
    function actionParseNew($className)
    {

        $class = new $className;
        $class->runJob();
        $json = $class->getLastParsedData();

        ParsersStock::model()->deleteAll(array('condition' => "`filename`='" . $class->getName() . "'"));


        foreach ($json['items'] as $item) {
            $new = new ParsersStock;
            $item = (array)$item;
            $item['filename'] = $json['name'];
            $item['name'] = substr($item['name'], 0, 99);

            if (ParsersLinking::model()->find(array(
                'condition' => 'fromId=:fromId',
                'params' => array(':fromId' => $item['id'])))
            ) {
                $item['linked'] = 1;
            }

            $new->attributes = $item;

            $new->save();
        }
        ob_clean();

        $tempfile = require(dirname(Yii::app()->request->scriptFile) . "/files/parsersData/time.txt");


        //   echo date("d.m.Y H:i", $tempfile[$class->getName()]);


    }

    public function actionDo($file, $tab = 'changed')
    {


        $catItems = array();
        $itemList = array();


        if ($tab == 'allSynched') {
            $itemList = ParsersLinking::model()->findAllByAttributes(array('filename' => $file), array('order' => 'id ASC'));

        }

        if ($tab == 'changedImages') {
            $combined = ParsersLinking::model()->findAllByAttributes(array('filename' => $file), array('order' => 'id ASC'));


            $parsedImages = array();

            if ($combined) {
                foreach ($combined as $item) {
                    if (isset($item->linking->images) & isset($item->item->id)) {
                        $newImages = [];

                        $images = json_decode($item->linking->images);

                        $parsedImages[$item->item->id]['item'] = $item;
                        $parsedImages[$item->item->id]['images'] = [];

                        $hashesMd5 = [];
                        $hashesSha1 = [];

                        foreach ($images as $image) {

                            $hashMd5 = hash_file('md5', $image);
                            $hashSha1 = hash_file('sha1', $image);

                            if (ParsersIgnoreImages::model()->findByAttributes(["md5" => $hashMd5, 'sha1' => $hashSha1])) {
                                continue;
                            }

                            if (!in_array($hashMd5, $hashesMd5) & !in_array($hashSha1, $hashesSha1)) {

                                $parsedImages[$item->item->id]['images'][] = [
                                    'md5' => $hashMd5,
                                    'sha1' => $hashSha1,
                                    'image' => $image,
                                    'imageUrl' => str_replace(Yii::getPathOfAlias('webroot'), '', $image)
                                ];

                                $hashesMd5[] = $hashMd5;
                                $hashesSha1[] = $hashSha1;
                            }
                        }

                        $itemData = array();
                        $datafile = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogItem/' . $item->item->id . '/data.php';

                        if (file_exists($datafile)) {
                            $itemData = require($datafile);


                            foreach ($itemData['images'] as $image) {
                                $hashMd5 = hash_file('md5', Yii::getPathOfAlias('webroot') . $image['original']);
                                $hashSha1 = hash_file('sha1', Yii::getPathOfAlias('webroot') . $image['original']);


                                foreach ($parsedImages[$item->item->id]['images'] as $key => $parsedImageOne) {
                                    if ($parsedImageOne['md5'] == $hashMd5 & $parsedImageOne['sha1'] == $hashSha1) {
                                        unset($parsedImages[$item->item->id]['images'][$key]);
                                    }
                                }
                            }
                        }

                        if (count($parsedImages[$item->item->id]['images']) == 0) {
                            unset($parsedImages[$item->item->id]);
                        }


                    }

                }

            }

            $itemList = $parsedImages;
        }

        if ($tab == 'changed') {
            $combined = ParsersLinking::model()->findAllByAttributes(array('filename' => $file), array('order' => 'id ASC'));

            if ($combined) {
                foreach ($combined as $item) {
                    if (isset($item->linking) & isset($item->item)) {
                        if ($item->linking->price != $item->item->price || $item->linking->quantity != $item->item->quantity) {
                            $itemList[] = $item;
                        }
                    }

                }

            }
        }

        if ($tab == 'ignoredImages') {
            $itemList = new ParsersIgnoreImages;
        }

        if ($tab == 'new' | $tab == 'newWithId') {
            $parserLinkingIds = ParsersLinking::model()->findAll();

            $iDsArray = array();
            foreach ($parserLinkingIds as $parserData) {
                $iDsArray[] = $parserData['toId'];
            }

            $criteria = new CDbCriteria();
            $criteria->addNotInCondition("id", $iDsArray);
            $criteria->order = 'name ASC';

            $catItems = CatItem::model()->findAll($criteria);
        }

        if ($tab == 'new') {

            $itemList = new ParsersStock;

            $itemList->filename = $file;
            $itemList->linked = 0;
            $itemList->ids = array();


        }

        if ($tab == 'newWithId') {

            $ids = array();
            foreach ($catItems as $item) {
                if ($item->article != '') {
                    $ids[] = $item->article;
                }

            }

            if (!$ids) {
                $ids = ["none"];
            }

            // print_r($ids);
            // return;

            $itemList = new ParsersStock;

            $itemList->filename = $file;
            $itemList->linked = 0;
            $itemList->ids = $ids;


        }

        if ($tab == 'allSynched') {
            $itemList = new ParsersLinking('searchb');

            $itemList->filename = $file;

            if (isset($_GET['ParsersLinking']))
                $itemList->Attributes = $_GET['ParsersLinking'];
        }

        if (isset($_GET['ParsersStock']))
            $itemList->Attributes = $_GET['ParsersStock'];


        $this->render('do', array(
            // 'models'=>$models,
            // 'return' => $return,
            'filename' => $file,
            'itemList' => $itemList,
            'allItems' => $catItems,
            'tab' => $tab,
        ));

    }

    public
    function deteleLinkingButton($data)
    {
        return "<input type='button' value='Удалить связь' data-id='{$data->id}' class='deleteLinking btn'>";
    }

    public
    function getSyncButtons($data, $row)
    {
        $return = "<button type='button'  data-filename='{$data->filename}' class='composite btn btn-info' name='{$data->name}'>Объединить с ...</button>";
        if ($data->findedByArticle()) {
            $return .= "<form action='/parsers/default/syncCard' data-removeAfter='.item-{$data->id}' class='ajaxSubmit'>
                <input type='hidden' name='ParsersLinking[fromId]' id='name' value='{$data->id}'><br/>
                <input type='hidden' name='ParsersLinking[toId]' id='itemId' value='{$data->findedByArticle()}' >
                <input type='hidden' name='ParsersLinking[filename]' id='itemId' value='{$data->filename}' >
                <button type='submit' class='compositeRightNow btn btn-primary' data-id='{$data->findedByArticle()}' title=''>Привязать сразу по артиклю к (<a style='color:white;text-decoration:underline' href='" . $this->createUrl('/catalog/catItem/update', array('id' => $data->findedByArticle())) . "'>{$data->findedByArticle()}</a>)</button></td>
            </form>";
        }
        $images = json_decode($data->images);

        if ($images) {

            $return .= "<div style='display:none'><table id='images-" . str_replace('/', '', $data->id) . "'>
                    <thead>
                        <tr>
                            <td>Изображение</td>
                            <td>Сохранить его?</td>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($images as $image) {
                $imageUrl = str_replace(Yii::getPathOfAlias('webroot'), '', $image);
                $return .= "<tr>
                                <td><img src='{$imageUrl}' width: 100px></td>
                                <td><input type='checkbox' name='images[]' value='{$image}'></td>
                            </tr>";
            }


            $return .= "</tbody>
                </table></div>";
        }

        return $return;
    }

    public
    function getAddAsNewButton($data)
    {
        return "<button type='button' class='addAsNew' data-filename='{$data->filename}' data-id='{$data->id}' data-price='{$data->price}' data-name='{$data->name}' data-images='{$data->images}'  data-parents='{$data->parents}' data-groups='{$data->groups}' data-text='{$data->text}' data-modif='{$data->modifs}'>Добавить как новый</button>";
    }

    public
    function actionLinking()
    {

        $model = ParsersLinking::model()->findAll(array('order' => 'filename ASC'));
        $buttons = array();

        foreach ($model as $item) {
            $filename = preg_replace('/\./', '', $item->filename);
            if (!in_array($filename, $buttons)) {
                $buttons[] = $filename;
            }
        }

        $this->render('linking', array(
            'buttons' => $buttons,
            'items' => $model
        ));
    }

    public function actionIndex()
    {

        $timeFile = array();

        $fileData = $this->getFiles();

        if (file_exists(Yii::app()->basePath . '/../files/parsersData/time.txt')) ;
        $timeFile = require(Yii::app()->basePath . '/../files/parsersData/time.txt');

        $this->render('index', array(
            // 'models'=>$models,
            // 'return' => $return,
            'fileListOfDirectory' => $fileData,

            'timeArray' => $timeFile
        ));

    }

    public function getFiles()
    {

        $fileListOfDirectory = array();
        $timeArray = array();


        $parserDataDir = Yii::getPathOfAlias('webroot') . '/files/parsersData/';
        $parserTimeFile = $parserDataDir . 'time.txt';


        if (!file_exists($parserDataDir)) {
            mkdir($parserDataDir);
        }

        if (!is_writable($parserDataDir)) {
            throw new Exception($parserDataDir . "не может быть изменена. Недостаточно прав", 503);

        }


        if (!file_exists($parserTimeFile)) {

            $myfile = fopen($parserTimeFile, "w");
            fclose($myfile);

            PictureBox::crPhpArr(array(), $parserTimeFile);
        }

        if (file_exists($parserTimeFile)) {
            $timeArray = require($parserTimeFile);
        }


        foreach (glob(Yii::app()->basePath . '/jobs/*ParserJob.php') as $path) {


            $className = basename($path);
            $className = str_replace('.php', '', $className);
            $class = new $className;


            $time = '';

            if (count($timeArray)) {
                foreach ($timeArray as $key => $value) {

                    if ($key == $class->getName()) {
                        $time = date("d.m.Y H:i", $value) . "<br/>";

                        break;

                    } else {
                        $time = "Еще не выполнялась<br/>";
                    }
                }
            }


            array_push($fileListOfDirectory, array('name' => $class->getName(), 'time' => $time, 'className' => $className));
        }


        return $fileListOfDirectory;
    }

    public
    function actionDeleteLinking($id)
    {
        $model = ParsersLinking::model()->findByPk($id);

        $stockModel = ParsersStock::model()->findByPk($model->fromId);
        if (isset($stockModel)) {
            $stockModel->linked = 0;
            $stockModel->save();
        }


        ob_clean();
        if ($model->delete()) {
            echo "1";
        } else {
            echo "0";
        }

    }

}
