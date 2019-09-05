<?php
Yii::setPathOfAlias('RestfullYii', '/home/sites/outhomeman.ru/protected/modules/begemot/extensions/RESTFullYii/starship/RestfullYii/');

class TaskPerformController extends Controller
{
    public $layout = 'begemot.views.layouts.column2content';

    public function actionTaskList($accessCode = null)
    {
        if (!is_null($accessCode)) {
            if ($contentTask = $this->checkAccessCode($accessCode)) {
                $this->render('index', ['task' => $contentTask]);
            } else {
                $this->redirect('/');
            }
        }
    }

    protected function checkAccessCode($accessCode)
    {
        $contentTask = ContentTask::model()->findByAttributes(['accessCode' => $accessCode]);
        if ($contentTask) {
            return $contentTask;
        } else {
            return false;
        }
    }


    public function actionTaskInfo($accessCode)
    {

        if ($contentTask = $this->checkAccessCode($accessCode)) {
            echo json_encode([
                'id' => $contentTask->id,
                'name' => $contentTask->name,
                'text' => $contentTask->text,
                'actions'=>unserialize($contentTask->actionsList)
            ]);
        }
    }

    public function actionEdit($accessCode, $id)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            $this->render('update');
        }
    }

    public function actionSave($accessCode, $id)
    {

        if ($contentTask = $this->checkAccessCode($accessCode)) {
            $contentTaskId = $contentTask->id;

            $added = ContentTaskAdded::model()->findByPK($id);


            if ($added->taskId == $contentTaskId && ($added->status == 'edit' || $added->status == 'new' || $added->status == 'mistake')) {
                $postdata = file_get_contents("php://input");
                $postdata = json_decode($postdata);

                $type = BaseDataType::factoryType($contentTask->type);

                foreach ($postdata->data as $fieldData) {

                    if ($fieldData->name==$type->tableFieldTitle){
                        $added->tmpName = $fieldData->data;
                    }

                    $attributes = [
                        'dataType' => $added->type,
                        'name' => $fieldData->name,
                        'iteration' => $added->iteration,
                        'isBaseData' => 0,
                        'subTaskId'=>$id
                    ];

                    $searchResult = ContentTaskData::model()->findByAttributes($attributes);

                    if (!$searchResult) {
                        $searchResult = new ContentTaskData();
                    }

                    $searchResult->dataType = $added->type;
                    $searchResult->name = $fieldData->name;
                    $searchResult->iteration = $added->iteration;
                    $searchResult->data = $fieldData->data;
                    $searchResult->groupId = $added->contentId;
                    $searchResult->taskId = $added->taskId;

                    if ($searchResult->save()) {
                        if ($added->status=='mistake'){
                            $added->status = "mistake";
                        } else {
                            $added->status = "edit";
                        }

                        $added->save();
                    }
                    echo count($searchResult);
                }
            }
        }
    }

    public function actionSendToReview($accessCode, $id)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            $contentTaskId = $contentTask->id;

            $added = ContentTaskAdded::model()->findByPK($id);

            if ($added->taskId == $contentTaskId && ($added->status == 'edit' || $added->status == 'new' || $added->status == 'mistake')) {
                $added->status = 'review';
                $added->save();
            }
        }
    }

    public function actionAjaxGetDataAndFields($accessCode, $id)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            $contentTaskId = $contentTask->id;

            $added = ContentTaskAdded::model()->findByPK($id);
            if ($added->taskId == $contentTaskId) {
                //обрубаем доступ тем, кто с ключем пытается попасть в другие задания
                $contentId = $added->contentId;

                /*
                 * Дальше нам нужно вытащить из базы и отдать интерфейсу базовые данные, которые есть образец,
                 *  и данные, которые можно менять.
                 */

                $data = ContentTaskData::model()->findAllByAttributes(
                    [
                        'taskId' => $contentTaskId,
                        'groupId' => $contentId,
                        'iteration' => $added->iteration,
                        'isBaseData' => 1 //база
                    ]
                );

                $resultBaseArray = [];


                foreach ($data as $row) {
                    $resultArrayRow = [];
                    $resultArrayRow['data'] = $row->data;
                    $resultArrayRow['dataType'] = $row->dataType;
                    $resultArrayRow['name'] = $row->name;
                    $resultBaseArray[] = $resultArrayRow;
                }

                $data = ContentTaskData::model()->findAllByAttributes(
                    [
                        'taskId' => $contentTaskId,
                        'groupId' => $contentId,
                        'iteration' => $added->iteration,
                        'isBaseData' => 0 // то что в работе
                    ]);


                $resultCurrentArray = [];
                if ($data) {
                    foreach ($data as $row) {
                        $resultArrayRow = [];
                        $resultArrayRow['data'] = $row->data;
                        $resultArrayRow['dataType'] = $row->dataType;
                        $resultArrayRow['name'] = $row->name;
                        $resultCurrentArray[] = $resultArrayRow;
                    }
                } else {
                    $resultCurrentArray = $resultBaseArray;
                }
                $result = [
                    'base' => $resultBaseArray,
                    'current' => $resultCurrentArray,
                    'status' => $added->status,
                    'addedId' => $added->id,
                    'iteration' => $added->iteration
                ];
                echo json_encode($result);
            } else {
                throw new Exception('Отказано в доступе');
            }
        }
    }

    public function actionAjaxAddedList($accessCode)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {

            $searchId = '';
            $searchTitle = '';
            $searchPage = 0;
            $searchType = null;

            if (isset($_REQUEST['id'])) {
                $searchId = $_REQUEST['id'];
            }
            if (isset($_REQUEST['title'])) {
                $searchTitle = $_REQUEST['title'];
            }
            if (isset($_REQUEST['page'])) {
                $searchPage = $_REQUEST['page'];
            }
            if (isset($_REQUEST['type'])) {
                $searchType = $_REQUEST['type'];
            }
            $taskId = $contentTask->id;

            $task = ContentTask::model()->findByPk($taskId);

            $providerName = $task->type;

            $providerInstance = BaseDataType::factoryDataProvider($providerName, $taskId);

            echo $providerInstance->addedSearch($searchId, $searchTitle, $searchPage, $searchType);
        }
    }

    public function actionAjaxStatusList($accessCode)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            $sql = "SELECT status,count(*) as count FROM ContentTaskAdded where taskId=" . $contentTask->id . " group by status ;";

            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $result = $command->query();

            $resultArray = [];

            while (($row = $result->read()) !== false) {

                $resultArray[$row['status']] = $row['count'];
            }
            echo json_encode($resultArray);
        }
    }

    public function actionIsAdmin()
    {

        $isAdmin = Yii::app()->user->canDo();
        if ($isAdmin) {

            echo "admin";
        } else {
            echo "noAdmin";
        }
    }

    /**
     * @param $accessCode Код доступа.
     * @param $id Id подзадания.
     */
    public function actionAjaxGetReviewData($accessCode, $id, $iteration)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {

            $reviewData = ContentTaskReviewData::model()->findByAttributes(['subtaskId' => $id, 'iteration' => $iteration]);
            if ($reviewData){

                echo $reviewData->jsonData;
            }

        }
    }

    public function actionAjaxReviewDataSave($accessCode, $id)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {

            //Админу можно все, не проверяем на принадлежность подзадания заданию
            //Не админ отправлять правки не может

            if (Yii::app()->user->canDo()) {

                $postdatajson = file_get_contents("php://input");
                $postdata = json_decode($postdatajson);

                $added = ContentTaskAdded::model()->findByPK($id);
                $currentIteration = $added->iteration;

                $reviewData = ContentTaskReviewData::model()->findByAttributes(['subtaskId' => $id, 'iteration' => $currentIteration]);
                if (!$reviewData) {
                    $reviewData = new ContentTaskReviewData();
                }

                $reviewData->iteration = $currentIteration;
                $reviewData->jsonData = $postdatajson;
                $reviewData->subtaskId = $id;
                $reviewData->save();
//                print_r($contentTask->id);


            }
        }
    }

    public function actionSendBackToWork($accessCode, $id)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            if (Yii::app()->user->canDo()) {
                $added = ContentTaskAdded::model()->findByPK($id);
                $added->status = "mistake";
                $added->iteration = $added->iteration + 1;
                if ($added->save()) {
                    //Создаем данные второй итерации
                    $contentData = ContentTaskData::model()->findAllByAttributes(
                        [
                            'subTaskId' => $added->id,
                            'isBaseData' => 0,
                            'iteration' => $added->iteration - 1
                        ]
                    );

                    foreach ($contentData as $singleContent) {
                        $nextIteration = new ContentTaskData();
                        $nextIteration->attributes = $singleContent->attributes;
                        $nextIteration->iteration = $added->iteration;
                        if ($nextIteration->save()) {
                            $nextIterationBase = new ContentTaskData();
                            $nextIterationBase->attributes = $nextIteration->attributes;
                            $nextIterationBase->isBaseData = 1;
                            $nextIterationBase->iteration = $added->iteration;
                            $nextIterationBase->save();
                        }

                    }
                }


            }
        }

    }

    public function actionAjaxCreateNew($accessCode){
        if ($contentTask = $this->checkAccessCode($accessCode)) {

            $type = $contentTask->type;
            Yii::import('contentTask.taskTypes.*');
            $model = new $type();
            $model->create($contentTask->id);
        }
    }
    public function actionAjaxPushToSite($accessCode,$taskId,$subtaskId){
        if ($contentTask = $this->checkAccessCode($accessCode)) {

        $type = BaseDataType::factoryType($contentTask->type);
            $type->export($taskId,$subtaskId);
        }
    }
    public function actionMarkAsDone($accessCode, $id)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            if (Yii::app()->user->canDo()) {
                $added = ContentTaskAdded::model()->findByPK($id);
                $added->status = "done";
                $added->save();


            }
        }
    }
    public function actionUpload ($accessCode){
        if ($contentTask = $this->checkAccessCode($accessCode)) {


            //Path to autoload.php from current location
            require_once Yii::getPathOfAlias('contentTask.components.Flow').'/File.php';
            require_once Yii::getPathOfAlias('contentTask.components.Flow').'/Basic.php';
            require_once Yii::getPathOfAlias('contentTask.components.Flow').'/RequestInterface.php';
            require_once Yii::getPathOfAlias('contentTask.components.Flow').'/Request.php';
            require_once Yii::getPathOfAlias('contentTask.components.Flow').'/ConfigInterface.php';
            require_once Yii::getPathOfAlias('contentTask.components.Flow').'/Config.php';


            $config = new \Flow\Config();
            echo Yii::getPathOfAlias('webroot').'/temp';
            $config->setTempDir(Yii::getPathOfAlias('webroot').'/temp/');
            $request = new \Flow\Request();
            $uploadFolder = Yii::getPathOfAlias('webroot').'/upload/'; // Folder where the file will be stored
            $uploadFileName = uniqid()."_".$request->getFileName(); // The name the file will have on the server
            $uploadPath = $uploadFolder.$uploadFileName;
            if (\Flow\Basic::save($uploadPath, $config, $request)) {
                echo "Показывает что сохранили";
            } else {
                // This is not a final chunk or request is invalid, continue to upload.
            }



        }
    }

}