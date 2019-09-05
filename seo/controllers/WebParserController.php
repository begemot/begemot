<?php

class WebParserController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
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
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view', 'tags'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete', 'tagProcess','unprocessedTagTaskCount'),
                'users' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionTags()
    {
        $this->render('tags');
    }

    /**
     * Обрабатывает одну случайную страницу и считает количество тегов
     */
    public function actionTagProcess()
    {
        Yii::import('seo.models.*');
        $pageModel = SeoPages::model()->findByAttributes(['tagsCoputedFlag' => 0]);
        if ($pageModel) {


            $pageId = $pageModel->id;
            $page = $pageModel->content;
            $options = array("indent" => true,
                "output-xml" => true,
                "clean" => true,
                "drop-proprietary-attributes" => true,
                "drop-font-tags" => true,
                "drop-empty-paras" => true,
                "hide-comments" => true,
                "join-classes" => true,
                "join-styles" => true,
                "show-body-only" => true);

            $tidy = new tidy();
            $str = $tidy->parseString($page, $options, 'utf8');
            $tidy->cleanRepair();




            $xmldata = $tidy;

            $dom = new DOMDocument(1, 'UTF-8');

            @$dom->loadHTML($xmldata);

            $node = $dom->getElementsByTagName('html');
            $currentNode = $node->item(0);

            $tags = [];
            $tagsCount = [];


            $this->nodeChildsWalk($currentNode, $tags, $tagsCount);


            $schema = Yii::app()->db->getSchema();
            $tables = $schema->tables;

            $columns = $tables['seo_tags']->columns;

            foreach ($tagsCount as $tagName => $value) {
                if (!isset($columns[$tagName])) {

                    $sql = $schema->addColumn('seo_tags', $tagName, 'INT');
                    Yii::app()->db->createCommand($sql)->execute();

                }
                $user = Yii::app()->db->createCommand()
                    ->select('id')
                    ->from('seo_tags')
                    ->where('pageId=:pageId', [':pageId' => $pageId])
                    ->queryRow();

                if (!$user) {
                    Yii::app()->db->createCommand()
                        ->insert('seo_tags', array(
                            'pageId' => $pageId,
                        ));
                }

                Yii::app()->db->createCommand()
                    ->update('seo_tags', array(
                        $tagName => $value,
                    ), 'pageId=:pageId', array(':pageId' => $pageId));


            }
            $pageModel->tagsCoputedFlag = 1;
            $pageModel->save();
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'done']);
        }
    }
    public function actionUnprocessedTagTaskCount(){

        $count = SeoPages::model()->countByAttributes(['tagsCoputedFlag' => 0]);
        echo json_encode($count+0);
    }
    private function nodeChildsWalk($currentNode, &$tags, &$tagsCount, $level = 0)
    {
        if ($currentNode->hasChildNodes()) {

            $nodeCurrentChild = $currentNode->firstChild;
            do {
                if ($nodeCurrentChild->nodeName == '#text') continue;
                if ($nodeCurrentChild->nodeName == '#cdata-section') continue;
                $nodeName = $nodeCurrentChild->nodeName;

                if (isset($tagsCount[$nodeName])) {
                    $tagsCount[$nodeName]++;
                } else {
                    $tagsCount[$nodeName] = 1;
                }

//            echo $nodeCurrentChild->nodeName;echo ' '.$level.' <br>';
                if ($nodeCurrentChild->hasChildNodes()) {
                    $this->nodeChildsWalk($nodeCurrentChild, $tags, $tagsCount, $level++);
                }
            } while ($nodeCurrentChild = $nodeCurrentChild->nextSibling);

        }
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
        $model = new WebParser;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['WebParser'])) {
            $model->attributes = $_POST['WebParser'];
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
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['WebParser'])) {
            $model->attributes = $_POST['WebParser'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex($processId = null)
    {


        $this->render('index', array(
            'processId' => $processId,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new WebParser('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['WebParser']))
            $model->attributes = $_GET['WebParser'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return WebParser the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = WebParser::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param WebParser $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'web-parser-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
