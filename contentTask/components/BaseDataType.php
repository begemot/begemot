<?php

class BaseDataType
{
    public $title = 'Название типа заданий';
    public $text = 'Описание типа задания';
    public $actions = [];

    public $tableName = '';
    public $tableFieldId  = 'id';
    public $tableFieldTitle = 'title';

    public $taskId = null;



    public function search($id,$title,$page=0)
    {
        $countPerPage = 20;
        $page = ($page-1)*$countPerPage;
        $sql = "select A.`".$this->tableFieldId.'`,A.`'.$this->tableFieldTitle."`,B.`taskId` as added from `".$this->tableName."` A
        
         left join  `ContentTaskAdded` B ON (A.`".$this->tableFieldId."` = B.`contentId`) AND  (B.`taskId` = ".$this->taskId.")
     
        where A.`".$this->tableFieldId."` like \"%".$id."%\" AND A.`".$this->tableFieldTitle."` like \"%".$title."%\"
         limit ".$page.",".$countPerPage. "
        ;";

        $countSql = "select count(*) from `".$this->tableName."`
        where `".$this->tableFieldId."` like \"%".$id."%\" AND `".$this->tableFieldTitle."` like \"%".$title."%\"

        ;";

        $connection=Yii::app()->db;

        $command = $connection->createCommand($countSql);
        $count = $command->queryScalar();


        $connection=Yii::app()->db;
        $command = $connection->createCommand($sql);
        $result = $command->query();

        $resultArray = [];

        while(($row=$result->read())!==false) {
            $resultArrayRow = [];
            $resultArrayRow['id'] = $row['id'];
            $resultArrayRow['title'] = $row[$this->tableFieldTitle];
            $resultArrayRow['added'] = $row['added'];
            $resultArray[]=$resultArrayRow;
        }

        echo json_encode(['data'=>$resultArray,'count'=>$count]);
    }

    public function addedSearch($id,$title,$page=0,$type=null)
    {
        $countPerPage = 20;
        $page = ($page-1)*$countPerPage;

        $typeSearch = '';
        if (!is_null($type)){
            $typeSearch = " AND B.`status`=\"".$type."\"";
        }

        $sql = "select A.`".$this->tableFieldId.'`,A.`'.$this->tableFieldTitle."`,
        B.`taskId` as added, B.`id` as subTaskId from `".$this->tableName."` A
        
         left join  `ContentTaskAdded` B ON A.`".$this->tableFieldId."` = B.`contentId` AND  (B.`taskId` = ".$this->taskId.")
     
        where A.`".$this->tableFieldId."` like \"%".$id."%\" 
        AND A.`".$this->tableFieldTitle."` like \"%".$title."%\"
        AND A.`".$this->tableFieldId."` = B.`contentId`".$typeSearch."
         limit ".$page.",".$countPerPage. "
        ;";



         $countSql = "select count(*) from `".$this->tableName."` A
        
         left join  `ContentTaskAdded` B ON A.`".$this->tableFieldId."` = B.`contentId` AND  (B.`taskId` = ".$this->taskId.")
     
        where A.`".$this->tableFieldId."` like \"%".$id."%\" 
        AND A.`".$this->tableFieldTitle."` like \"%".$title."%\"
        AND A.`".$this->tableFieldId."` = B.`contentId` ".$typeSearch."
        ;";

        $connection=Yii::app()->db;

        $command = $connection->createCommand($countSql);
        $count = $command->queryScalar();


        $connection=Yii::app()->db;
        $command = $connection->createCommand($sql);
        $result = $command->query();

        $resultArray = [];

        while(($row=$result->read())!==false) {
            $resultArrayRow = [];
            $resultArrayRow['id'] = $row['id'];
            $resultArrayRow['subTaskId'] = $row['subTaskId'];

            $resultArrayRow['title'] = $row[$this->tableFieldTitle];
            $resultArray[]=$resultArrayRow;
        }

        echo json_encode(['data'=>$resultArray,'count'=>$count]);
    }

    public static function factoryDataProvider($name,$taskId){

        $modulwPath = Yii::getPathOfAlias('contentTask');
        $filePath = $modulwPath . '/taskTypes/' . $name . '.php';

        if (file_exists($filePath)) {
            require_once($filePath);
            $instance = new $name();
            $instance->taskId = $taskId;
        }
        return $instance;
    }



    public function getDataFields(){
        return [];
    }

    public static function factoryType($type){

        $modulwPath = Yii::getPathOfAlias('contentTask');
        $filePath = $modulwPath . '/taskTypes/' . $type . '.php';

        if (file_exists($filePath)) {
            require_once($filePath);
            $instance = new $type();
        }
        return $instance;
    }

    public static function getDataTypesList(){

        $modulePath = Yii::getPathOfAlias('contentTask');

        $dirs = glob($modulePath.'/taskTypes/*');
        $typesList = [];
        foreach ($dirs as $typeFilePath){
            require_once($typeFilePath);
            $oneType = [];
            $className = basename($typeFilePath);
            $info = pathinfo($className);
            $typeInstanc = new $info['filename']();
            $oneType = [
                'id'=>$info['filename'],
                'title'=>$typeInstanc->title
            ];
            $typesList[]=$oneType;
        }
//        print_r($typesList);
        return $typesList;
    }

    public function create($taskId){
        echo 'Создаем новую позицию без связей';
    }

    /**
     * выкатываем на сайт, обратная операция import
     *
     * @param $subtaskId идентификатор подзадания
     * @param $taskId идентификатор задания
     */
    public function export($subtaskId, $taskId)
    {
        echo "экспортируем на сайт";
    }


}