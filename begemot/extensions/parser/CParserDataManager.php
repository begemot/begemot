<?php

/**
 * Created by PhpStorm.
 * User: anton
 * Date: 11.10.15
 * Time: 23:04
 */
class CParserDataManager
{

    private $processId;

    public function CParserDataManager($processId)
    {
        $this->processId = $processId;
    }

    /**
     * Убираем все промежуточные данные и
     * пробрасываем родительские связи
     */
    public function cutDataTree()
    {

        $sql = "
        SELECT * FROM webParserData
         WHERE  fieldName like :search
         and processId=:processId
         LIMIT 1";
        $searchParams = array(':search' => '-%','processId'=>$this->processId);

        while ($tempDataCollection = WebParserData::model()->findBySql($sql, $searchParams)){

            $tempData = $tempDataCollection;

            $tempData->delete();
        }
    }

    public function getDataTreeArray()
    {
        $this->cutDataTree();

        $resultArray = [];

        $tempDataCollection = WebParserData::model()->findAll("processId=" . (int)$this->processId);

        foreach ($tempDataCollection as $tempData) {
            if (!isset($resultArray[$tempData->fieldId])) {
                $resultArray[$tempData->fieldId] = [];
            }

            $resultArray[$tempData->fieldId][$tempData->fieldName] = $tempData->fieldData;
            $parentFieldId = $tempData->fieldParentId;
            if (!is_null($parentFieldId)) {

                if (!isset($resultArray[$tempData->fieldId]['parents'])) {
                    $resultArray[$tempData->fieldId]['parents'] = [];
                }
                $resultArray[$tempData->fieldId]['parents'][] = $parentFieldId;
            }

        }

        return $resultArray;
    }

    public function getFilesArray(){


        $WebParserDownloadArray = WebParserDownload::model()->findAll(array(
            'condition'=>'processId=:processId',
            'params'=>array(':processId'=> $this->processId ),
        ));

        $resultArray = [];

        foreach ($WebParserDownloadArray as $WebParserDownload){
        
            if (!isset ($resultArray[$WebParserDownload->fieldId]) || !is_array($resultArray[$WebParserDownload->fieldId]))
            {
                $resultArray[$WebParserDownload->fieldId] = [];
            }
            $resultArray[$WebParserDownload->fieldId][] = $WebParserDownload->file;

        }

        return $resultArray;
    }

    public function getChildsArray (){

        $WebParserDataArray = WebParserData::model()->findAll(array(
            'condition'=>'processId=:processId',
            'params'=>array(':processId'=> $this->processId ),
            'select'=>'fieldId,fieldParentId',
            'distinct'=>true,
        ));


        $resultArray = [];

        foreach ($WebParserDataArray as $WebParserData){



            if ($WebParserData->fieldParentId!==null){
                if (!isset ($resultArray[$WebParserData->fieldId]) || !is_array($resultArray[$WebParserData->fieldId]))
                {
                    $resultArray[$WebParserData->fieldId] = [];
                }
                $resultArray[$WebParserData->fieldId][] = $WebParserData->fieldParentId;
            }

        }

        return $resultArray;
    }

    public function getModifArray (){

        $resultArray = [];

        $WebParserDataArray = WebParserData::model()->findAll(array(
            'condition'=>'processId=:processId',
            'params'=>array(':processId'=> $this->processId ),
            'select'=>'fieldId,fieldModifId',
            'distinct'=>true,
        ));

        foreach ($WebParserDataArray as $WebParserData){
            if ($WebParserData->fieldModifId!==null){
                $resultArray[$WebParserData->fieldId] = $WebParserData->fieldModifId;
            }
        }

        return $resultArray;
    }

    public function getChildsGroupsArray (){

        $WebParserDataArray = WebParserData::model()->findAll(array(
            'condition'=>'processId=:processId',
            'params'=>array(':processId'=> $this->processId ),
            'select'=>'fieldId,fieldGroupId',
            'distinct'=>true,
        ));


        $resultArray = [];

        foreach ($WebParserDataArray as $WebParserData){



            if ($WebParserData->fieldGroupId!==null){
                if (!isset ($resultArray[$WebParserData->fieldId]) || !is_array($resultArray[$WebParserData->fieldId]))
                {
                    $resultArray[$WebParserData->fieldId] = [];
                }
                $resultArray[$WebParserData->fieldId][] = $WebParserData->fieldGroupId;
            }

        }

        return $resultArray;
    }

    public function getFieldNameByDataId($dataId)
    {

        $field = WebParserData::model()->findByPk($dataId);
        if (!is_null($field)){
            return $field->fieldId;
        }
    }
} 