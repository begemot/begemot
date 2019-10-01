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
                'actions' => unserialize($contentTask->actionsList)
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


            if ($added->taskId == $contentTaskId && ($added->status == 'edit' || $added->status == 'new' || $added->status == 'mistake' || $added->status == 'done')) {
                $postdata = file_get_contents("php://input");
                $postdata = json_decode($postdata);

                $type = BaseDataType::factoryType($contentTask->type);

                foreach ($postdata->data as $fieldData) {

                    if ($fieldData->name == $type->tableFieldTitle) {
                        $added->tmpName = $fieldData->data;
                    }

                    $attributes = [
                        'dataType' => $added->type,
                        'name' => $fieldData->name,
                        'iteration' => $added->iteration,
                        'isBaseData' => 0,
                        'subTaskId' => $id
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
                        if ($added->status == 'mistake' || $added->status == 'done') {

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

//                $data = ContentTaskData::model()->findAllByAttributes(
//                    [
//                        'taskId' => $contentTaskId,
//                        'groupId' => $contentId,
//                        'iteration' => $added->iteration,
//                        'isBaseData' => 1 //база
//                    ]
//                );

                $resultBaseArray = $this->getContentTaskData($contentTaskId, $contentId, $added, 1);


//                foreach ($data as $row) {
//                    $resultArrayRow = [];
//                    $resultArrayRow['data'] = $row->data;
//                    $resultArrayRow['dataType'] = $row->dataType;
//                    $resultArrayRow['name'] = $row->name;
//                    $resultBaseArray[] = $resultArrayRow;
//                }

//                $data = ContentTaskData::model()->findAllByAttributes(
//                    [
//                        'taskId' => $contentTaskId,
//                        'groupId' => $contentId,
//                        'iteration' => $added->iteration,
//                        'isBaseData' => 0 // то что в работе
//                    ]);


                $resultCurrentArray = $this->getContentTaskData($contentTaskId, $contentId, $added, 0);

                if (!$resultCurrentArray) {
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

    public function getContentTaskData($contentTaskId, $contentId, $added, $isBaseData = 1)
    {
        $data = ContentTaskData::model()->findAllByAttributes(
            [
                'taskId' => $contentTaskId,
                'groupId' => $contentId,
                'iteration' => $added->iteration,
                'isBaseData' => $isBaseData,

            ]
        );

        $resultArray = [];


        Yii::import('seo.models.SeoCheck');

        foreach ($data as $row) {
            $seoCheck = SeoCheck::model()->findByAttributes(['uid' => $row->uid]);
            $resultArrayRow = [];
            $resultArrayRow['data'] = $row->data;
            $resultArrayRow['dataType'] = $row->dataType;
            $resultArrayRow['name'] = $row->name;
            $resultArrayRow['uid'] = $row->uid;

            if ($seoCheck)
                $resultArrayRow['checkResult'] = $seoCheck->attributes;

            $resultArray[] = $resultArrayRow;
        }
        return $resultArray;
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

            $resultArray = [
                'new' => 0,
                'edit' => 0,
                'review' => 0,
                'mistake' => 0,
                'done' => 0
            ];

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
            if ($reviewData) {

                echo $reviewData->jsonData;
            } else {
                $added = ContentTaskAdded::model()->findByPK($id);
                $data = $this->getContentTaskData($contentTask->id, $added->contentId, $added, 0);
                echo json_encode(['visibleData' => $data]);
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

    public function actionAjaxCreateNew($accessCode)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {

            $type = $contentTask->type;
            Yii::import('contentTask.taskTypes.*');
            $model = new $type();
            $model->create($contentTask->id);
        }
    }

    public function actionAjaxPushToSite($accessCode, $taskId, $subtaskId)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {

            $type = BaseDataType::factoryType($contentTask->type);

            $model = ContentTaskAdded::model()->findByAttributes(['taskId' => $taskId, 'id' => $subtaskId]);
            $model->exported = 1;
            if ($model->save()) {

                $type->export($taskId, $subtaskId);
            } else {
                throw new Exception('Модель не сохранилась!');
            }
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

    public function actionSendCheckRequest($accessCode, $id, $name, $mode)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            if (Yii::app()->user->canDo()) {

                $model = ContentTaskAdded::model()->findByAttributes(['taskId' => $contentTask->id, 'id' => $id]);
                echo 'Отправляем запрос на проверку!';
                $iteration = $model->iteration;
                $attr = [
                    'subTaskId' => $id,
                    'iteration' => $iteration,
                    'isBaseData' => 0,
                    'name' => $name
                ];

                $data = ContentTaskData::model()->findByAttributes($attr);
                if ($data && ($data->uid != '' && !$mode)) {
                    throw new Exception("Данные уже запрашивались! Ждем ответа от text.ru");
                } else {
                    Yii::import('seo.models.SeoCheck');
                    $seoCheck = new SeoCheck();
                    if ($uid = $seoCheck->sendCheckRequest($data->data)) {
                        $data->uid = $uid;
                        if (!$data->save()) {
                            throw new Exception("Не смогли сохранить uid");
                        }
                    } else {
                        throw new Exception("Ошибка отправки запроса на text.ru");
                    }
                }
            }
        }
    }

    public function actionUpdateCheckRequest($accessCode, $id, $name, $uid)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            if (Yii::app()->user->canDo()) {
//                echo 'Отправляем запрос на обновление';
                Yii::import('seo.models.SeoCheck');
                $seoCheck = new SeoCheck();
                $seoCheck->uid = $uid;
                if ($seoCheck->getCheckResult()) {
                    echo json_encode($seoCheck->attributes);
                }
            }
        }
    }

    public function actionDataFields($accessCode)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            $sql = 'SELECT distinct(name)  FROM rosvesdehod.ContentTaskData where taskId='.$contentTask->id.' ;';



            $connection=Yii::app()->db;
            $command = $connection->createCommand($sql);
            $result = $command->query();

            $resultArray = [];

            while(($row=$result->read())!==false) {
                $resultArrayRow = [];
                $resultArrayRow['name'] = $row['name'];

                $resultArray[]=$resultArrayRow;
            }
            echo json_encode($resultArray);
        }
    }

    public function actionloadAuditData($accessCode,$name)
    {
        if ($contentTask = $this->checkAccessCode($accessCode)) {
            $attr = [
                'taskId'=>$contentTask->id,
                'status'=>'done'
            ];

            $resultArray = [];

            $doneAdded = ContentTaskAdded::model()->findAllByAttributes($attr);
            foreach ($doneAdded as $addedItem){
                $dataAttr=[
                    'subTaskId'=>$addedItem->id,
                    'iteration'=>$addedItem->iteration,
                    'name'=>$name,
                    'isBaseData'=>0
                ];
                $contentDataArray = ContentTaskData::model()->findAllByAttributes($dataAttr);
                foreach ($contentDataArray as $contentData){
                    $resultLine = [];
                    $resultLine['tmpName'] = $addedItem->tmpName;
                    $resultLine['subTaskId'] = $addedItem->id;
                    $resultLine['uid'] = $contentData['uid'];
                    $resultLine['name'] = $contentData['name'];
                    $resultLine['dataId'] = $contentData['id'];
                    if ($contentData['uid']){
                        Yii::import('seo.models.SeoCheck');
                        $seoCheck = SeoCheck::model()->findByAttributes(['uid'=>$contentData['uid']]);
                        if ($seoCheck){
                            $resultLine['seoCheck'] = $seoCheck->attributes;
                        }
                    }

                    $resultArray[] = $resultLine;
                }
            }
            echo json_encode($resultArray);
        }
    }
}