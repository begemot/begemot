<?php
Yii::setPathOfAlias('RestfullYii', '/home/sites/outhomeman.ru/protected/modules/begemot/extensions/RESTFullYii/starship/RestfullYii/');

class ContentTaskController extends Controller
{
    public $layout = 'begemot.views.layouts.column2';

    public function accessRules()
    {
        return array(
            array('allow', 'actions' => array('REST.GET',
                'REST.PUT', 'REST.POST', 'REST.DELETE', 'REST.OPTIONS','added','admin','delete','editableSaver',
                'ajaxRemoveFromTask','ajaxAddedSearch', 'create', 'ajaxSearch','ajaxAddTOTask', 'typesList', 'update', 'search', 'test','ajaxGenerateCode'
            , 'typeActs', 'typeFields', 'createAndSerrialise'),
                'users' => array('*'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

//    public function init()
//    {
//
//        Yii::app()->onException = function ($event) {
//            echo 123;
//        };
//
//    }

    public function actionCreate()
    {
        $contentTask = new ContentTask();
        $contentTask->generateAccessCode();
        $contentTask->codeDate = time();
        if ($contentTask->save()) {
            $this->redirect('/contentTask/contentTask/update?id=' . $contentTask->id, TRUE, 301);
        }
    }

    public function actionUpdate()
    {
        $this->render('update');
    }
    public function actionAdded()
    {
        $this->render('added');
    }
    public function actionSearch()
    {
        $this->render('search');
    }

    public function actionAjaxGenerateCode ($id) {
        $model = $this->loadModel($id);
        $model->generateAccessCode();
        if ($model->save()){
            echo $model->accessCode;
        }
    }

    public function actionAjaxSearch($taskId)
    {
        $searchId = '';
        $searchTitle = '';
        $searchPage = 0;
        if (isset($_REQUEST['id'])) {
            $searchId = $_REQUEST['id'];
        }
        if (isset($_REQUEST['title'])) {
            $searchTitle = $_REQUEST['title'];
        }
        if (isset($_REQUEST['page'])) {
            $searchPage = $_REQUEST['page'];
        }

        $task = ContentTask::model()->findByPk($taskId);

        $providerName = $task->type;


        $providerInstance = BaseDataType::factoryDataProvider($providerName,$task->id);

        echo $providerInstance->search($searchId, $searchTitle, $searchPage);
    }

    public function actionAjaxAddedSearch($taskId)
    {
        $searchId = '';
        $searchTitle = '';
        $searchPage = 0;
        if (isset($_REQUEST['id'])) {
            $searchId = $_REQUEST['id'];
        }
        if (isset($_REQUEST['title'])) {
            $searchTitle = $_REQUEST['title'];
        }
        if (isset($_REQUEST['page'])) {
            $searchPage = $_REQUEST['page'];
        }

        $task = ContentTask::model()->findByPk($taskId);

        $providerName = $task->type;


        $providerInstance = BaseDataType::factoryDataProvider($providerName,$task->id);

        echo $providerInstance->addedSearch($searchId, $searchTitle, $searchPage);
    }

    public function actionAjaxAddTOTask($taskId,$id){
        $contentTask = ContentTask::model()->findByPk($taskId);
        $type = $contentTask->type;
        Yii::import('contentTask.taskTypes.*');
        $model = new $type();
        $model->import($id, $taskId);
    }

    public function actionAjaxRemoveFromTask($taskId,$id){
        $contentTasksAdded = ContentTaskAdded::model()->findAllByAttributes(['taskId'=>$taskId,'contentId'=>$id]);
        foreach ($contentTasksAdded as $contentTask){
            $contentTask->delete();
        }
    }

    public function actionTypesList()
    {
        $array = BaseDataType::getDataTypesList();
        echo $json = json_encode($array);
    }


    public function actionCreateAndSerrialise()
    {
        $postdata = file_get_contents("php://input");

        print_r($postdata);

        $postdata = json_decode($postdata);


        if (isset($postdata->id)) {


            $contentTask = ContentTask::model()->findByPK($postdata->id);
            $contentTask->name = $postdata->name;
            $contentTask->text = $postdata->text;
            $contentTask->type = $postdata->type;
            $contentTask->saveActions($postdata->actionsList);
            $contentTask->saveFields($postdata->fieldsList);

            if ($contentTask->save()) {
                echo $contentTask->id;
            }
        } else {
            throw new Exception();
        }

//        print_r($postdata);
    }

    public function actionTypeActs($type, $id)
    {

        /*
         * Отдаем список операций и цепляем выбранные типы для конкретных заданий
         */
        $type = BaseDataType::factoryType($type);

        $contentTask = ContentTask::model()->findByPk($id);
        if ($contentTask->actionsList){
            $data = unserialize($contentTask->actionsList);
        } else {
            $data = [];
        }

        foreach ($type->actions as $key => $action) {
            foreach ($data as $selectedAction) {
                if ($selectedAction->id == $action['id']) {
                    $type->actions[$key]['selected'] = '1';
                }
            }
        }

        echo json_encode($type->actions);
    }

    public function actionTypeFields($type, $id)
    {

        $typeInstance = BaseDataType::factoryType($type);
        $fields = $typeInstance->getDataFields();

        $contentTask = ContentTask::model()->findByPk($id);
        if ($contentTask->dataElementsList){
            $data = unserialize($contentTask->dataElementsList);
        } else {
            $data = [];
        }

//        print_r($fields);
//        print_r($data);

        foreach ($fields as $key => $field) {
            foreach ($data as $selectedField) {

                if ($key == $selectedField['name']) {
                    $fields[$key]['selected'] = 1;
                }
            }
        }

        echo json_encode($fields);
    }

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            array(
                'RestfullYii.filters.ERestFilter +
			 	REST.GET, REST.PUT, REST.POST, REST.DELETE'
            ),
        );
    }

    public function actions()
    {
        return array(
            'REST.' => 'RestfullYii.actions.ERestActionProvider',
        );
    }

    public function actionAdmin()
    {
        $model=new ContentTask('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['ContentTask']))
            $model->attributes=$_GET['ContentTask'];

        $this->render('admin',array(
            'model'=>$model,
        ));
    }


    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionTest()
    {
        Yii::import('contentTask.taskTypes.Catalog');
        $model = new Catalog();
        $model->import(930, 32);
    }
    public function loadModel($id)
    {
        $model=ContentTask::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
//    public function restEvents()
//    {
//
//        $this->onRest('post.filter.req.auth.ajax.user', function($validation) {
////            if(!$validation) {
////
////                return false;
////            }
//            switch ($this->getAction()->getId()) {
//                case 'REST.GET':
//                    return true;
//                    break;
//                case 'REST.POST':
//                    return Yii::app()->user->checkAccess('REST-CREATE');
//                    break;
//                case 'REST.POST':
//                    return Yii::app()->user->checkAccess('REST-UPDATE');
//                    break;
//                case 'REST.DELETE':
//                    return true;
//                    break;
//                default:
//                    return false;
//                    break;
//            }
//        });
//    }
    public function actionEditableSaver()
    {
        Yii::import('begemot.extensions.bootstrap.widgets.TbEditableSaver');
        $es = new TbEditableSaver('ContentTask');
        $es->update();
    }
}