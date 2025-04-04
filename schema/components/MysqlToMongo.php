<?php class MysqlToMongo
{

    public static function checkCollection($collectionName): bool
    {
        $database = Yii::app()->mongoDb->getDb();
        // Получение списка коллекций в базе данных
        $collections = $database->listCollections();

        // Проверка, существует ли коллекция
        $collectionExists = false;
        foreach ($collections as $collection) {
            if ($collection->getName() === $collectionName) {
                $collectionExists = true;
                break;
            }
        }

        // Вывод результата
        if ($collectionExists) {
            return true;
        } else {
            return false;
        }
    }

    public static function syncSchemaFields()
    {
        $schemaFieldMaxId = -1;

        $collection = Yii::app()->mongoDb->getCollection('schemaField');
        $counterCollection = Yii::app()->mongoDb->getCollection('counters');
        Yii::import('schema.models.SchemaField');
        $res = SchemaField::model()->findAll();
        foreach ($res as $schemaFieldElem) {

            if ($collection->countDocuments(['_id' => (int)$schemaFieldElem->id]) != 0) continue;

            if ($schemaFieldMaxId < $schemaFieldElem->id) {
                $schemaFieldMaxId = $schemaFieldElem->id;
            }
            $data = $schemaFieldElem->getAttributes();
            $data['_id'] = (int)$data['id'];
            unset($data['id']);
            $data['schemaId'] = (int)$data['schemaId'];
            $collection->insertOne($data);
            // print_r($schemaFieldElem->getAttributes());
        }



        if ($counterCollection->countDocuments(['_id' => 'schemaFieldId']) == 0) {
            $opRes = $counterCollection->insertOne(['_id' => 'schemaFieldId', 'value' => (int)$schemaFieldMaxId]);
        } else {
            // Фильтр для поиска документа (например, по полю _id)
            $filter = ['_id' => 'schemaFieldId'];

            // Новые данные для обновления
            $update = [
                '$set' => [
                    'value' => (int)$schemaFieldMaxId
                ]
            ];
            $opRes = $counterCollection->updateOne($filter, $update);
        }
    }

    public static function syncSchemaData()
    {


        $collection = Yii::app()->mongoDb->getCollection('schemaData');
        $collectionField = Yii::app()->mongoDb->getCollection('schemaField');

        Yii::import('schema.models.SchemaLinks');
        Yii::import('schema.components.CSchemaLink');
        Yii::import('schema.mongoModels.MngSchemaFieldModel');

        $schemas = SchemaLinks::model()->findAll();

        foreach ($schemas as $schemaLink) {
            $linkType = $schemaLink->linkType;
            $groupId = $schemaLink->linkId;
            $schemaId = $schemaLink->schemaId;

            $query = [
                'groupId' => (int)$groupId,
                'schemaId' => (int)$schemaId,
                'linkType' => $linkType

            ];
            $count = $collection->countDocuments($query);
            if ($count > 0) continue;

            // $CSchemaLink = new CSchemaLink($linkType, $groupId, $schemaId);
            // $res = $CSchemaLink->getData();
            // print_r($res);
            $mongoDataLine = [];
            $mongoDataLine['groupId'] = (int)$groupId;
            $mongoDataLine['linkType'] = $linkType;
            $mongoDataLine['schemaId'] = (int)$schemaId;
            $engineSchemaLink = new CSchemaLink($linkType, $groupId, $schemaId);
            $data = [];
            $data = $engineSchemaLink->getSchemasFieldsData();

            foreach ($data as $fieldName => $dataItem) {

                $type = MngSchemaFieldModel::findById($dataItem['fieldId'])->type;
                if ($type == 'Int') {
                    $mongoDataLine['fields'][$fieldName]['value'] = (int)$dataItem['value'];
                } else {
                    $mongoDataLine['fields'][$fieldName]['value'] = $dataItem['value'];
                }
                $mongoDataLine['fields'][$fieldName]['fieldId'] = (int)$dataItem['fieldId'];
            }
            // $mongo = Yii::app()->mongoDb->getCollection('schemaData');
            $collection->insertOne($mongoDataLine);
            // return;
        }
        return;
        $result = CSchmEngine::findAll();
        // print_r($result);
        $mongoData = [];
        foreach ($result as $item) {
            $mongoDataLine = [];
            $mongoDataLine['groupId'] = (int)$item->linkId;
            $mongoDataLine['linkType'] = 'engine';
            $mongoDataLine['schemaId'] = 2;
            $engineSchemaLink = new CSchemaLink('engine', $item->linkId);
            $data = [];
            $data = $engineSchemaLink->getData();
            $data = array_shift($data);
            $data = $data['data'];
            foreach ($data as $dataItem) {


                $type = MngSchemaFieldModel::findById($dataItem['id'])->type;
                if ($type == 'Int') {
                    $mongoDataLine['fields'][$dataItem['name']]['value'] = (int)$dataItem['value'];
                } else {
                    $mongoDataLine['fields'][$dataItem['name']]['value'] = $dataItem['value'];
                }

                $mongoDataLine['fields'][$dataItem['name']]['fieldId'] = $dataItem['id'];
            }
            $mongo = Yii::app()->mongoDb->getCollection('schemaData');
            $mongo->insertOne($mongoDataLine);
            $mongoData[] = $mongoDataLine;
        }
    }
}
