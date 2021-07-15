<?php
/**
 * Created by PhpStorm.
 * User: Антон
 * Date: 21.02.2021
 * Time: 21:59
 */

class CSchemaLink
{
    private $linkType;
    private $linkedDataId;
    private $fieldsAndDataArray = null;

    public function CSchemaLink($linkType,$linkedDataId)
    {
        $this->linkType = $linkType;
        $this->linkedDataId = $linkedDataId;
    }

    public function get($fieldName){
        if (is_null($this->fieldsAndDataArray)){
            $this->fieldsAndDataArray = $this->getSchemasFieldsData();
        }

        return $this->fieldsAndDataArray[$fieldName]['value'];
    }

    /**
     * @param $groupId
     * @param $linkype
     *
     * Достает структуру по одной схеме. ищет все подсхемы
     * достает все field всех схем.
     *
     * @return array
     */
     public function getSchemasFields( )
    {
        $linkType = $this->linkType;
        $groupId = $this->linkedDataId;
        $schemas = self::getAllSchemas($groupId, $linkType);

        $schemasIds = array_column($schemas, 'id');


        return $fields = Yii::app()->db->createCommand()->select('*')->
        from('SchemaField')->
        where(['in', 'schemaId', $schemasIds])
            ->queryAll();

    }

    function getAllSchemas()
    {
        $linkType = $this->linkType;
        $groupId = $this->linkedDataId;

        $allSchemas = Yii::app()->db->createCommand()->select('*')->
        from('SchemaLinks')->
        where('linkType=:linkType and linkId=:groupId', [':groupId' => $groupId, ':linkType' => $linkType])
            ->queryAll();

        $allSchemasIds = array_column($allSchemas, 'schemaId');
        $schemasIdsResult = $allSchemasIds;

        while (count($allSchemasIds) > 0) {

            $childsSchemas = Yii::app()->db->createCommand()->select('*')->
            from('Schema')->
            where(['in', 'pid', $allSchemasIds])
                ->queryAll();

            $allSchemasIds = array_column($childsSchemas, 'id');
            $schemasIdsResult = array_merge($schemasIdsResult, $allSchemasIds);


        }

        $schemasIdsResult;
        return Yii::app()->db->createCommand()->select('*')->
        from('Schema')->
        where(['in', 'id', $schemasIdsResult])
            ->queryAll();
    }


    public function getSchemasFieldsData()
    {
        $linkType = $this->linkType;
        $groupId = $this->linkedDataId;

        $fields = self::getSchemasFields($groupId, $linkType);
        $fieldsId = array_column($fields, 'id');

        $query = $data = Yii::app()->db->createCommand()->select(
            '*,tb1.value as textValue'
        )->
        from('SchemaData')->
        where(
            ['and',
                'groupId=:groupId',
                'linkType=:linkId',
                ['in', 'fieldId', $fieldsId],
            ]
            , [
                ':groupId' => $groupId,
                ':linkId' => $linkType,
            ]
        );
        $query = $query->leftJoin('SchmTypeText tb1', 'SchemaData.id=tb1.fieldDataId');
        $query = $query->leftJoin('SchemaField tb3', 'SchemaData.fieldId=tb3.id');
        $fieldsAndData = $query->leftJoin('SchmTypeString tb2', 'SchemaData.id=tb2.fieldDataId')->queryAll();

        $fieldsNames = array_column($fieldsAndData,'name');
        return array_combine($fieldsNames,$fieldsAndData);
    }

    /**
     * @param $groupId
     * @param $linkype
     * @return array
     *
     * Собирает данные, по ним собирает все нужные field, schema и data
     * Если по какому-то field для link нет данных, то эта функция не
     * вернет этот field.
     *
     * Относительно быстро собирает данные
     *
     *
     */
    public function getData()
    {
        $linkType = $this->linkType;
        $groupId = $this->linkedDataId;

        Yii::import('schema.models.types.*');

        $dataArray = Yii::app()->db->createCommand()->select('*')->
        from('SchemaData')->
        where('groupId=:id and linkType=:linkType', array(':id' => $groupId, 'linkType' => $linkType))->queryAll();

        $fieldsIdArray = array_column($dataArray, 'fieldId', 'id');

        $fieldsArray = Yii::app()->db->createCommand()->select('*')->
        from('SchemaField')->
        where(array('in', 'id', $fieldsIdArray))
            ->queryAll();

        $fieldsIdsArray = array_column($fieldsArray, 'id');
        $fieldsArray = array_combine($fieldsIdsArray, $fieldsArray);
        $schemaIdArray = array_column($fieldsArray, 'schemaId', 'id');

        $schemaArray = Yii::app()->db->createCommand()->select('*')->
        from('Schema')->
        where(array('in', 'id', $schemaIdArray))
            ->queryAll();
        $schemaArrayIds = array_column($schemaArray, 'id');
        $schemaArray = array_combine($schemaArrayIds, $schemaArray);
        // $arrayId = array_column($fields, 'name', 'id');
        foreach ($dataArray as $key => $data) {

            $dataFieldId = $data['fieldId'];
            if (isset($fieldsArray[$dataFieldId])) {
//                $arrayId[$dataFieldId] = [
//                    'name' => $arrayId[$dataFieldId],
//                ];
                $fieldsArray[$dataFieldId]['value'] = SchmTypeString::model()->findByPk($data['valueId'])->value;
//                $arrayId[$dataFieldId]['value'] = SchmTypeString::model()->findByPk($data['valueId'])->value;
                $schemaId = $fieldsArray[$dataFieldId]['schemaId'];

                if (isset($schemaArray[$schemaId])) {
                    $schemaArray[$schemaId]['data'][] = $fieldsArray[$dataFieldId];
                }

            }
        }


        return $schemaArray;

    }


}