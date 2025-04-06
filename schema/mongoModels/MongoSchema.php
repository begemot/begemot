<?php class MongoSchema
{
    public static function getSchemaByNameOrCreate($name,$pid= 0)
    {
        $collection =  Yii::app()->mongoDb->getCollection('schema');

        if (!self::checkMongoSchemaExistByName($name)) {
            self::mysqlSync();
        }

        if (!self::checkMongoSchemaExistByName($name)) {
            Yii::import('schema.components.MongoCounters');
            $counters = new MongoCounters();
            $insertData = [
                '_id' => $counters->getNextValue('schema'),
                'name' => $name,
                'pid'=>(int)$pid
            ];
        }
        return $collection->findOne(['name' => $name]);
    }

    public static function mysqlSync()
    {
        Yii::import('schema.models.Schema');
        $schemas = Schema::model()->findAll();
        $collection =  Yii::app()->mongoDb->getCollection('schema');
        $baseId = 0;
        foreach ($schemas as $schema) {
            $mysqlSchemaId = $schema->id;
            if (!self::checkMongoSchemaExistById($schema->id)) {
                if ($baseId < $schema->id) {
                    $baseId = $schema->id;
                }
                $schemaData = [
                    '_id' => (int)$schema->id,
                    'name' => $schema->name,
                    'pid' => (int)$schema->pid,
                ];
                $collection->insertOne($schemaData);
            }
        }

        if ($baseId != 0) {

            $collectionCounters =  Yii::app()->mongoDb->getCollection('counters');
            $collectionCounters->insertOne([
                '_id' => 'schema',
                'value' => (int)$baseId
            ]);
        }
    }

    public static function checkMongoSchemaExistById($schemaId)
    {
        $collection =  Yii::app()->mongoDb->getCollection('schema');
        if ($collection->countDocuments(['_id' => $schemaId]) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function checkMongoSchemaExistByName($schemaName)
    {
        $collection =  Yii::app()->mongoDb->getCollection('schema');
        if ($collection->countDocuments(['name' => $schemaName]) == 0) {
            return false;
        } else {
            return true;
        }
    }
}
