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
    public static function checkTableExists($tableName)
    {
        $db = Yii::app()->db;

        $tables = $db->schema->getTables();
        return isset($tables[$tableName]);
    }

    public static function copyMysqlToMongodb(
        $mysqlTableName,
        $mongoCollectionName = null,
        $batchSize = 1000
    ) {
        // Получаем подключения
        $mysqlConn = Yii::app()->db;
        $mongoDb = Yii::app()->mongoDb;

        // Определяем имя коллекции в MongoDB
        $mongoCollectionName = $mongoCollectionName ?: $mysqlTableName;
        $collection = $mongoDb->getCollection($mongoCollectionName);

        // Очищаем целевую коллекцию
        $collection->deleteMany([]);

        // Получаем метаданные о столбцах таблицы MySQL
        $columnsInfo = $mysqlConn->getSchema()->getTable($mysqlTableName)->columns;

        // Получаем данные из MySQL
        $command = $mysqlConn->createCommand("SELECT * FROM `{$mysqlTableName}`");
        $dataReader = $command->query();

        $maxId = null;
        $batch = [];

        // Читаем данные пачками
        while (($row = $dataReader->read()) !== false) {
            // Конвертируем типы данных согласно метаданным таблицы
            foreach ($row as $key => $value) {
                if ($value === null) {
                    continue;
                }

                if (!isset($columnsInfo[$key])) {
                    continue;
                }

                $columnType = $columnsInfo[$key]->dbType;
                $columnName = $columnsInfo[$key]->name;

                // Приводим типы данных согласно типу столбца в MySQL
                switch (true) {
                    // Целочисленные типы
                    case strpos($columnType, 'int') !== false:
                    case strpos($columnType, 'tinyint') !== false:
                    case strpos($columnType, 'smallint') !== false:
                    case strpos($columnType, 'mediumint') !== false:
                    case strpos($columnType, 'bigint') !== false:
                        $row[$key] = (int)$value;
                        break;

                    // Числа с плавающей точкой
                    case strpos($columnType, 'decimal') !== false:
                    case strpos($columnType, 'float') !== false:
                    case strpos($columnType, 'double') !== false:
                        $row[$key] = (float)$value;
                        break;

                    // Булевы значения (особенно для tinyint(1))
                    case $columnType === 'tinyint(1)':
                        $row[$key] = (bool)$value;
                        break;

                    // Даты и временные метки
                    case strpos($columnType, 'date') !== false:
                    case strpos($columnType, 'time') !== false:
                    case strpos($columnType, 'year') !== false:
                        if ($value !== '0000-00-00' && $value !== '0000-00-00 00:00:00') {
                            try {
                                $row[$key] = new MongoDB\BSON\UTCDateTime(new DateTime($value));
                            } catch (Exception $e) {
                                // Оставляем как строку в случае ошибки парсинга
                                Yii::log("Failed to parse date '{$value}' for column {$columnName}: " . $e->getMessage(), CLogger::LEVEL_WARNING);
                            }
                        }
                        break;

                    // Бинарные данные
                    case is_resource($value):
                        $row[$key] = new MongoDB\BSON\Binary(stream_get_contents($value));
                        break;

                        // По умолчанию оставляем как есть (строки)
                }
            }

            // Обновляем максимальный ID
            if (isset($row['id']) && ($maxId === null || $row['id'] > $maxId)) {
                $maxId = $row['id'];
            }

            $batch[] = $row;

            // Вставляем пачками
            if (count($batch) >= $batchSize) {
                $collection->insertMany($batch);
                $batch = [];
            }
        }

        // Вставляем оставшиеся документы
        if (!empty($batch)) {
            $collection->insertMany($batch);
        }

        // Если нашли максимальный ID - сохраняем в коллекции counters
        if ($maxId !== null) {
            try {
                $countersCollection = $mongoDb->getCollection('counters');
                $filter = ['_id' => $mysqlTableName];
                $update = ['$set' => ['value' => (int)$maxId]];
                $options = ['upsert' => true];
                $countersCollection->updateOne($filter, $update, $options);
            } catch (Exception $e) {
                Yii::log("Failed to update counters collection: " . $e->getMessage(), CLogger::LEVEL_ERROR);
            }
        }

        return $maxId;
    }


    public static function migrate()
    {
        $tablesMaria = [
            'Schema',
            'SchemaData',
            'SchemaField',
            'SchemaLinks',
            'SchmGroup',
            'SchmTypeInt',
            'SchmTypeString',
            'SchmTypeText',
            'SchemaUnitOfMeasurement'
        ];

        // Инициализируем пустой массив для хранения результатов
        $tableMariaStatus = [];

        foreach ($tablesMaria as $table) {
            // Проверяем существование таблицы и добавляем результат в массив
            $exists = self::checkTableExists($table);
            $tableMariaStatus[$table] = $exists;
        }

        $tablesMongo = [
            'schemaUnitOfMeasurement',
            'schema',
            'schemaData',
            'schemaGroup',
            'schemaField'
        ];

        $tableMongoStatus = [];

        foreach ($tablesMongo as $table) {
            $exists = self::checkCollection($table);
            $tableMongoStatus[$table] = $exists;
        }

        // $result = ['1' => $tableMariaStatus, '2' => $tableMongoStatus];
        if (!$tableMongoStatus['schema']) {
            self::copyMysqlToMongodb('Schema', 'schema');
        }

        if (!$tableMongoStatus['schemaUnitOfMeasurement']) {
            self::copyMysqlToMongodb('SchemaUnitOfMeasurement', 'schemaUnitOfMeasurement');
        }

        if (!$tableMongoStatus['schemaField']) {
            self::syncSchemaFields();
        }


        if (!$tableMongoStatus['schemaData']) {
            self::syncSchemaData();
        }
    }
}
