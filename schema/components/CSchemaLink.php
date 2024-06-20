<?php

Yii::import('schema.models.*');

/**
 * Class CSchemaLink
 *
 * Набор функций для работы со схемами и конкретными данными.
 */
class CSchemaLink
{
    private $linkType;
    private $linkedDataId;
    private $fieldsAndDataArray = null;

    private $schemaLinkDb = null;

    public static function getMaxId($schemaLinkType)
    {
        $sql = "SELECT MAX(linkId) AS maxLinkId FROM SchemaLinks WHERE linkType = :linkType";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":linkType", $schemaLinkType);
        $result = $command->queryRow();
        $maxLinkId = $result['maxLinkId'];
        return  $maxLinkId;
    }

    public function createFieldAndData($fieldId, $value, $dataType)
    {
        Yii::import('schema.models.*');

        $model = SchemaField::model()->findByAttributes([
            'name' => $fieldId,
            'schemaId' => $this->schemaLinkDb->schemaId
        ]);
        if (!$model) {
            $model = new SchemaField();
            $model->name = $fieldId;
            $model->schemaId = $this->schemaLinkDb->schemaId;
            $model->type = $dataType;
        }

        if ($model->save()) {
            $model->setFieldData($value, $fieldId, $this->linkedDataId, $this->linkType);
        } else {
            throw new Exception('Не создалось');
        }
    }

    public function setData($fieldId, $value, $dataType = 'String')
    {

        $oldValue = $this->get($fieldId);

        if ($oldValue == 'no data') {
            //такого поля field нет ни у одной схемы прикрепленной к этой сущности
            //нужно создать field и data
            $this->createFieldAndData($fieldId, $value, $dataType);
        } else {

            $this->set($fieldId, $value, $this->linkType, $dataType);
        }
    }

    public function set($fieldId, $value, $type)
    {
        $model = SchemaField::model()->findByAttributes([
            'name' => $fieldId,
            'schemaId' => $this->schemaLinkDb->schemaId
        ]);
        $linkType = $this->linkType;
        if (!$model) {

            $model = new SchemaField();
            $model->name = $fieldId;
            $model->type = $type;
            $model->schemaId = $this->schemaLinkDb->schemaId;


            if ($model->save()) {
                $model->setData($fieldId, $value, $this->schemaLinkDb->schemaId, $this->linkedDataId, $linkType);
            } else
                throw new Exception('не получилось сохранить');
        } else
            $model->setData($fieldId, $value, $this->schemaLinkDb->schemaId, $this->linkedDataId, $linkType);

        $fieldsAndData = $this->getSchemasFieldsData(true);

        if (isset($fieldsAndData[$fieldId])) {
            if ($model->type == 'String') {
                $fieldsAndData[$fieldId]['value'] =  $value;
            } else {
                $fieldsAndData[$fieldId][$model->type . 'Value'] =  $value;
            }

            //::TODO непонятно что за место
            Yii::import('modules.components.ModulesManager');
            if (ModulesManager::isModuleActive('cache')) {
                Yii::import('cache.models.*');
                $cache = new Cache();
                $cacheGroup = __CLASS__ . '_getSchemasFieldsData';
                $cacheKey = $this->linkType . '_' . $this->linkedDataId;

                $cache->setValue($cacheGroup, $cacheKey, $fieldsAndData);
            }
        } else {
            throw new Exception('нет такого $fieldId: ' . $fieldId);
        }
    }

    public function __construct($linkType, $linkedDataId, $schemaId = null, $name = null)
    {
        $this->linkType = $linkType;
        $this->linkedDataId = $linkedDataId;


        $model = SchemaLinks::model()->findByAttributes([
            'linkType' => $linkType,
            'linkId' => $linkedDataId
        ]);

        if ($model) {

            $this->schemaLinkDb = $model;
        } else {
            if ($schemaId == null) {

                throw new Exception('Нужно создать SchemaLinks, но не понятно в какую схему.');
            } else {
                $model = new SchemaLinks();
                $model->linkType = $linkType;
                $model->linkId = $linkedDataId;
                $model->schemaId = $schemaId;
                $model->name = $name;
                if (!$model->save()) {
                    throw new Exception('Не удалось создать SchemaLink');
                } else {
                    $this->schemaLinkDb = $model;
                }
            }
        }
    }

    public function get($fieldName)
    {
        if (is_null($this->fieldsAndDataArray)) {

            $this->fieldsAndDataArray = $this->getSchemasFieldsData();
        }



        if (isset($this->fieldsAndDataArray[$fieldName]['value'])) {
            return $this->fieldsAndDataArray[$fieldName]['value'];
        } else {
            return 'no data';
        }
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
    public function getSchemasFields()
    {
        $linkType = $this->linkType;
        $groupId = $this->linkedDataId;
        $schemas = self::getAllSchemas(/*$groupId, $linkType*/);

        $schemasIds = array_column($schemas, 'id');


        return $fields = Yii::app()->db->createCommand()->select('*')->from('SchemaField')->where(['in', 'schemaId', $schemasIds])
            ->queryAll();
    }

    public function isSchemaInstanceExist()
    {

        $linkType = $this->linkType;

        $groupId = $this->linkedDataId;

        $fields = self::getSchemasFields($groupId, $linkType);

        $fieldsId = array_column($fields, 'id');

        $query = $data = Yii::app()->db->createCommand()->select(
            '*'
        )->from('SchemaData')->where(
            [
                'and',
                'groupId=:groupId',
                'linkType=:linkId',
                ['in', 'fieldId', $fieldsId],
            ],
            [
                ':groupId' => $groupId,
                ':linkId' => $linkType,
            ]
        );

        $fieldsAndData = $query->queryRow();

        if ($fieldsAndData) {
            return true;
        } else {
            return false;
        }
    }

    function getAllSchemas()
    {
        $linkType = $this->linkType;
        $groupId = $this->linkedDataId;

        $allSchemas = Yii::app()->db->createCommand()->select('*')->from('SchemaLinks')->where('linkType=:linkType and linkId=:groupId', [':groupId' => $groupId, ':linkType' => $linkType])
            ->queryAll();

        $allSchemasIds = array_column($allSchemas, 'schemaId');
        $schemasIdsResult = $allSchemasIds;


        while (count($allSchemasIds) > 0) {

            $childsSchemas = Yii::app()->db->createCommand()->select('*')->from('Schema')->where(['in', 'pid', $allSchemasIds])
                ->queryAll();

            $allSchemasIds = array_column($childsSchemas, 'id');
            $schemasIdsResult = array_merge($schemasIdsResult, $allSchemasIds);
        }

        $schemasIdsResult;
        return Yii::app()->db->createCommand()->select('*')->from('Schema')->where(['in', 'id', $schemasIdsResult])
            ->queryAll();
    }


    public function getSchemasFieldsData($getCached = false)
    {


        Yii::import('modules.components.ModulesManager');

        //Проверяем подключен ли модуль кеша
        if (ModulesManager::isModuleActive('cache')) {
            Yii::import('cache.models.*');

            $cache = new Cache();
            $cacheGroup = __CLASS__ . '_getSchemasFieldsData';
            $cacheKey = $this->linkType . '_' . $this->linkedDataId;
            $fieldsAndData = $cache->getValue($cacheGroup, $cacheKey);
        } else {
            $fieldsAndData = null;
        }


        if ($getCached || !$fieldsAndData) {


            $linkType = $this->linkType;
            $groupId = $this->linkedDataId;


            $fields = self::getSchemasFields($groupId, $linkType);

            $fieldsId = array_column($fields, 'id');

            $query = $data = Yii::app()->db->createCommand()->select(
                '*,tb1.value as TextValue'
            )->from('SchemaData')->where(
                [
                    'and',
                    'groupId=:groupId',
                    'linkType=:linkId',
                    ['in', 'fieldId', $fieldsId],
                ],
                [
                    ':groupId' => $groupId,
                    ':linkId' => $linkType,
                ]
            );
            $query = $query->leftJoin('SchmTypeText tb1', 'SchemaData.id=tb1.fieldDataId');
            $query = $query->leftJoin('SchemaField tb3', 'SchemaData.fieldId=tb3.id');
            $query = $query->join('SchmTypeString tb2', 'SchemaData.id=tb2.fieldDataId');
            $fieldsAndData = $query->queryAll();


            $fieldsNames = array_column($fieldsAndData, 'name');

            $fieldsAndData = array_combine($fieldsNames, $fieldsAndData);
            if (ModulesManager::isModuleActive('cache')) {
                $cache->setValue($cacheGroup, $cacheKey, $fieldsAndData);
            }
        }

        return $fieldsAndData;
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
    public function getData($cacheOff = false)
    {
        $linkType = $this->linkType;
        $groupId = $this->linkedDataId;
        Yii::import('modules.components.ModulesManager');
        if (ModulesManager::isModuleActive('cache')) {
            Yii::import('cache.models.*');
            $cache = new Cache();
            $cacheKey = __CLASS__ . '_' . 'getData_' . $groupId . '_' . $linkType;
            $schemaArray = $cache->getValue(__CLASS__ . '_' . 'getData_', $groupId . '_' . $linkType);
        } else $schemaArray = null;

        if ($cacheOff || !$schemaArray) {


            Yii::import('schema.models.types.*');

            $dataArray = Yii::app()->db->createCommand()
                ->select('*')
                ->from('SchemaData')
                ->where('groupId=:id and linkType=:linkType', array(':id' => $groupId, 'linkType' => $linkType))
                ->queryAll();


            $fieldsIdArray = array_column($dataArray, 'fieldId', 'id');

            $fieldsArray = Yii::app()->db->createCommand()
                ->select('*')
                ->from('SchemaField')
                ->where(array('in', 'id', $fieldsIdArray))
                ->queryAll();

            $fieldsIdsArray = array_column($fieldsArray, 'id');
            $fieldsArray = array_combine($fieldsIdsArray, $fieldsArray);
            $schemaIdArray = array_column($fieldsArray, 'schemaId', 'id');

            $schemaArray = Yii::app()->db->createCommand()
                ->select('*')
                ->from('Schema')
                ->where(array('in', 'id', $schemaIdArray))
                ->queryAll();
            $schemaArrayIds = array_column($schemaArray, 'id');
            $schemaArray = array_combine($schemaArrayIds, $schemaArray);

            foreach ($dataArray as $key => $data) {
                $dataFieldId = $data['fieldId'];

                if (isset($fieldsArray[$dataFieldId])) {
                    $typeModel = SchmTypeString::model()->findByPk($data['valueId']);

                    if (!$typeModel) {
                        continue;
                    }

                    $fieldsArray[$dataFieldId]['value'] = $typeModel->value;
                    $schemaId = $fieldsArray[$dataFieldId]['schemaId'];

                    if (isset($schemaArray[$schemaId])) {
                        $schemaArray[$schemaId]['data'][] = $fieldsArray[$dataFieldId];
                    }
                }
            }
            if (ModulesManager::isModuleActive('cache'))
                $cache->setValue(__CLASS__ . '_' . 'getData_', $groupId . '_' . $linkType, $schemaArray);
        } else {

            $schemaArray = $schemaArray;
        }
        return $schemaArray;
    }
}