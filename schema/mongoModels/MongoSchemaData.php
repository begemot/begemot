<?php
class MongoSchemaData
{

    private $data;
    private $collection;
    public function __construct(
        private int $groupId,
        private string $linkType,
        private int $schemaId
    ) {
        $this->collection = Yii::app()->mongoDb->getCollection("schemaData");
        $this->getData();
    }

    private function getData()
    {
        $collection = $this->collection;
        $count = $collection->countDocuments(['groupId' => $this->groupId, 'linkType' => $this->linkType, 'schemaId' => $this->schemaId]);

        if ($count == 1) {
            $this->data = $collection->findOne(['groupId' => $this->groupId, 'linkType' => $this->linkType, 'schemaId' => $this->schemaId]);
        } else {
            $res = $collection->insertOne(
                [
                    'groupId' => $this->groupId,
                    'linkType' => $this->linkType,
                    'schemaId' => $this->schemaId,

                ]
            );
            $this->data = $collection->findOne(['_id' => $res->getInsertedId()]);
        }
        $this->data = iterator_to_array($this->data);
    }

    public function setFieldValueByName($fieldName, $value)
    {
        $schemaField = MngSchemaFieldModel::findByName($fieldName);
        if (is_null($schemaField)) {
            $tmp = 'stop';
        }
        $fieldId = null;
        if ($schemaField) {
            $fieldId = $schemaField->_id;
        }
        $collection = $this->collection;
        if ($schemaField->type == 'Int') {
            $value = (int)$value;
        }
        if ($schemaField->type == 'String') {
            $value = (string)$value;
        }
        $collection->updateOne(
            ['groupId' => $this->groupId, 'linkType' => $this->linkType, 'schemaId' => $this->schemaId],
            ['$set' => ['fields.' . $fieldName => ['value' => $value, 'fieldId' => $fieldId]]]
        );
    }
    /**
     * Задаем список полей для группировки. Возвращает массив вида 
     * 
     *  fieldId =>[value=>groupIds,value2=>groupIds2]
     * 
     * 
     * @param mixed $fieldId_value_array
     * @param mixed $linkType
     * @param mixed $schemaId
     * @return array<array>
     */
    public static function getPackedData($fieldId_value_array, $linkType, $schemaId, $groupId = null)
    {
        if (is_null($groupId)) {
            $query = ['linkType' => $linkType, 'schemaId' => $schemaId];
            foreach ($fieldId_value_array as $key => $value) {
                $query['fields.' . $key . '.value'] = $value;
            }
        } else {
            $query = ['groupId' => (int)$groupId];
        }

        $schemaDataCollection = Yii::app()->mongoDb->getCollection('schemaData');
        $res = $schemaDataCollection->find($query);
        $pack = [];
        foreach ($res as $schemaDataValue) {
            $groupId = $schemaDataValue->groupId;
            foreach ($schemaDataValue->fields as $fieldName => $fieldValue) {
                if (!isset($pack[$fieldValue['fieldId']]))
                    $pack[$fieldValue['fieldId']] = [];

                if (!isset($pack[$fieldValue['fieldId']][$fieldValue['value']])) {
                    $pack[$fieldValue['fieldId']][$fieldValue['value']] = $groupId;
                }
            }
            $groupId = null;
            // print_r($schemaDataValue->getArrayCopy());
        }

        return $pack;
    }

    public static function getAllDataWithSchemaTree($filterFields, $linkType, $schemaId, $groupId = null)
    {

        $data = MongoSchemaData::getPackedData($filterFields, $linkType, $schemaId, $groupId);
        $data = array_map(function ($item) {
            return array_keys($item)[0];
        }, $data);
        $fieldIds = array_keys($data);
        $fieldsCollection = Yii::app()->mongoDb->getCollection('schemaField');
        $query = [
            '_id' => ['$in' => $fieldIds],

        ];

        $fields = $fieldsCollection->find($query);

        $fieldsData = [];
        $schemasIds = [];
        foreach ($fields as $field) {
            // print_r($field['name']);
            $fieldsData[$field->schemaId][] = $field;
            $schemasIds[] = $field->schemaId;
        }
        $schemas = Yii::app()->mongoDb->getCollection('schema')->find(['_id' => ['$in' => $schemasIds]])->toArray();

        $resData = ['schemas' => $schemas, 'schemaFields' => $fieldsData, 'data' => $data];
        if (!is_null($groupId)) {
            $schemaData = Yii::app()->mongoDb->getCollection('schemaData')->findOne(['groupId' => (int)$groupId]);
            $resData['schemaData'] = $schemaData;
        }
        return $resData;
    }

    public function getSortedData($limit = 0)
    {
        if (isset($this->data['fields'])) {
            Yii::import('schema.mongoModels.MngSchemaFieldModel');
            $fields = MngSchemaFieldModel::getAllFields();

            foreach ($fields as $field) {

                if (isset($this->data['fields'][$field['name']])) {
                    $this->data['fields'][$field['name']]['order'] = $field['order'];
                }
            }
            // Создаем копию массива для сортировки
            $sortedFields = $this->data['fields']->getArrayCopy();

            // Сортируем массив по полю order
            uasort($sortedFields, function ($a, $b) {
                $orderA = $a->order ?? 0;
                $orderB = $b->order ?? 0;
                return $orderA <=> $orderB;
            });

            // Выводим отсортированный результат

            return $sortedFields;
        }
        return false;
    }
}
