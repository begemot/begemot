<?php
class SchemaGroupManager
{

    private $mongoSchemaGroup; //коллекция mongoDb

    private $groupsFromData;
    private $existingGroups;
    private $notExistingGroups;

    public function __construct(
        private array $groupParams,
        private string $linkType,
        private array $baseGroupId = []
    ) {

        $this->groupParams = $groupParams;
        $this->linkType = $linkType;
        $this->baseGroupId = $baseGroupId;
        $this->mongoSchemaGroup = Yii::app()->mongoDb->getCollection('schemaData');
    }
    /**
     * Группируем данные schemaData по заданным параметрам и в рамках $baseGroupId
     */
    public function getGroups()
    {
        $fieldsIdArray = $this->groupParams;
        $linkType = $this->linkType;
        $baseGroupId = $this->baseGroupId;
        $collection = $this->mongoSchemaGroup;



        ksort($fieldsIdArray);

        $filter = [
            // 'schemaId' => $schemaId,
            'linkType' => $linkType,
        ];

        // Если передан список groupId, добавляем условие

        if (!empty($baseGroupId)) {
            $filter['groupId'] = ['$in' => $baseGroupId];
        }


        $id = [];
        $existMatch = [];
        foreach ($fieldsIdArray as $field) {
            $id[$field] = '$fields.' . $field;
            $filter['fields.' . $field] = ['$exists' => true];
        }

        $pipeline = [
            [
                '$match' => $filter
            ],
            [
                '$group' => [
                    '_id' => $id,
                    'customerIds' => [
                        '$addToSet' => '$groupId'
                    ]
                ]
            ]
        ];

        $cursor = $collection->aggregate($pipeline);
        $resultGroupsFromData = [];
        foreach ($cursor as $document) {

            $resultGroup['ids'] = iterator_to_array($document['customerIds']);
            // Преобразуем _id в нужный формат
            $idObject = $document->_id;
            $params = [];
            foreach ($idObject as $key => $value) {

                $params[$value['fieldId']] = $value['value'];
            }
            $resultGroup['params'] = $params;

            $resultGroupsFromData[] = $resultGroup;
        }

        return $resultGroupsFromData;
    }
    /**
     * Берем группировку данных и находим уже созданные до этого 
     * schemaGroup. 
     */
    public function getExistingSchemaGroup($groupData)
    {

        $res = array_map(function ($item) {
            $resitem = [];
            foreach ($item['params'] as $key => $value) {
                $resitem['params.' . $key] = $value;
            }
            // $resitem['ids'] = $item['ids'];
            return $resitem;
        }, $groupData);

        $collection = Yii::app()->mongoDb->getCollection('schemaGroup');
        $query = ['$or' => $res];
        $cursor = $collection->find($query);
        return $cursor->toArray();
    }

    /**
     * Берем группировку данных и список уже существующих групп, что бы определить 
     * какие группы мы находим в первый раз, для последующего создания экземпляра 
     * schemaGroup
     */
    public function getNotExistingSchemaGroup($groupData, $existingGroups)
    {
        // Функция для сравнения параметров
        function compareParams($params1, $params2)
        {
            return $params1 == $params2;
        }

        $firstCollection = $groupData;
        $secondCollection = $existingGroups;
        // Поиск групп из первой коллекции, которые отсутствуют во второй
        $missingGroups = [];

        foreach ($firstCollection as $group) {
            $isFound = false;

            // Параметры текущей группы из первой коллекции
            $params1 = $group['params'];

            // Поиск во второй коллекции
            foreach ($secondCollection as $instance) {
                $params2 = $instance['params']->getArrayCopy();

                if (compareParams($params1, $params2)) {
                    $isFound = true;
                    break; // Группа найдена, переходим к следующей
                }
            }

            // Если группа не найдена, добавляем её в результат
            if (!$isFound) {
                $missingGroups[] = $group;
            }
        }
        return $missingGroups;
    }



    public function createSchemaGroup($params)
    {
        if (empty($params)) return;
        Yii::import('schema.components.MongoCounters');
        $mc = new MongoCounters();


        $document = [
            'title' => $this->generateName($params),
            'params' => $params,
            'schemaGroup' => $mc->getNextValue('schemaGroup')
        ];
        $schemaGroupCollection = Yii::app()->mongoDb->getCollection('schemaGroup');
        if ($schemaGroupCollection->insertOne($document)) {
            return $document;
        } else {
            return false;
        }
        // echo $mc->getNextValue('testCounter');
        // print_r($params);
    }
    private function generateName($params)
    {
        $str = '';
        foreach ($params as $param) {
            $str .= ' ' . $param;
        }
        return $str;
    }

    public static function getAllGroups()
    {
        $schemaGroupCollection = Yii::app()->mongoDb->getCollection('schemaGroup');
        $res = $schemaGroupCollection->find([])->toArray();
        return $res;
    }

    public static function getAllGroupIds($groupId)
    {
        $schemaGroupCollection = Yii::app()->mongoDb->getCollection('schemaGroup');

        $query = ['schemaGroup' => (int)$groupId];
        $res = $schemaGroupCollection->findOne($query);
        $queryParams = [];

        Yii::import('schema.mongoModels.MngSchemaFieldModel');
        foreach ($res->params->getArrayCopy() as $key => $value) {
            $mongoFieldModel = MngSchemaFieldModel::findById((int)$key);

            $queryParams['fields.' . $mongoFieldModel->name . '.value'] = $value;
        }

        $pipeline = [
            ['$match' => $queryParams],
            ['$group' => [
                '_id' => null,
                'groupIds' => ['$addToSet' => '$groupId']
            ]]
        ];

        $schemaDataCollection = Yii::app()->mongoDb->getCollection('schemaData');
        $mongoResult = $schemaDataCollection->aggregate($pipeline)->toArray();
        return $mongoResult[0]->getArrayCopy()['groupIds']->getArrayCopy();
    }
}
