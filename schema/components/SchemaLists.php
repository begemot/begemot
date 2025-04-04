<?php

Yii::import('schema.models.*');
Yii::import('schema.models.types.*');

class SchemaLists
{
    public static function fromTolList(string $fieldName, $schemaId, string $linkType, array $groupIdList = [], $fromValue = null, $toValue = null): array
    {

        return self::getList($fieldName, $schemaId, ['from' => $fromValue, 'to' => $toValue], $linkType, $groupIdList);
    }

    public static function equalList(string $fieldName, $schemaId, $value, string $linkType, array $groupIdList = null): array
    {
        if (!is_array($value)) {
            $field = SchemaField::getSchemaFieldByName($fieldName, $schemaId);
            // $result = self::getCahedEqualList($linkType, $field->id, $value);

            // if (!$result) {
            $result = self::getList($fieldName, $schemaId, $value, $linkType);
            // self::setCahedEqualList($linkType, $field->id, $value, $result);
            // }

            if ($groupIdList === null) {
                return $result;
            } elseif (count($groupIdList) > 0) {
                return array_values(array_intersect($result, $groupIdList));
            } elseif (count($groupIdList) == 0) {
                return [];
            }
        } else {
            $resultArray = [];
            foreach ($value as $item) {
                $resultArray = array_merge($resultArray, self::equalList($fieldName, $schemaId, $item,  $linkType,  $groupIdList));
            }
            return $resultArray;
        }
    }

    public static function setCahedEqualList($linkType, $fieldId, $value, $data)
    {
        Yii::import('cache.models.Cache');
        $equalListCache = new Cache();
        $cacheGroup = 'Schema.equalList.' . $linkType;
        $cacheKey = $fieldId . '-' . $value;
        $result = $equalListCache->setValue($cacheGroup, $cacheKey, $data);
    }

    public static function getCahedEqualList($linkType, $fieldId, $value)
    {
        Yii::import('cache.models.Cache');
        $equalListCache = new Cache();
        $cacheGroup = 'Schema.equalList.' . $linkType;
        $cacheKey = $fieldId . '-' . $value;
        return $equalListCache->getValue($cacheGroup, $cacheKey);
    }

    /**
     * @param $fieldName имя поля по которому ищем совпадение
     * @param $value значение(или массив значений) которое проверяем на совпадение
     * @param $linkType тип связи
     * @param $groupIdList массив groupId, если уже есть предварительный список groupId, что бы делать каскад фильтраций
     * @return array
     *
     * Возвращает массив groupId связанных сущностей, например $linkType=CatItem,
     * у которых совпадают значения поля  fieldId с $value
     */
    public static function getList(string $fieldName, $schemaId, $value, string $linkType, array $groupIdList = []): array
    {
        // Базовый фильтр

        $client = Yii::app()->mongoDb->getDb();

        // Выбираем базу данных и коллекцию
        $collection = $client->schemaData;

        $filter = [
            'schemaId' => $schemaId,
            'linkType' => $linkType,
        ];

        // Если передан список groupId, добавляем условие

        if (!empty($groupIdList)) {
            $filter['groupId'] = ['$in' => $groupIdList];
        }

        // Формируем условие для fields
        $fieldKey = "fields.$fieldName.value";

        if (is_string($value) || is_int($value)) {
            // Простое значение
            $filter[$fieldKey] = $value;
        } elseif (is_array($value)) {
            if (isset($value['from']) || isset($value['to'])) {
                // Диапазон from-to
                $rangeFilter = [];
                if (isset($value['from'])) {
                    $rangeFilter['$gte'] = (int)$value['from'];
                }
                if (isset($value['to'])) {
                    $rangeFilter['$lte'] = (int)$value['to'];
                }
                $filter[$fieldKey] = $rangeFilter;
            } else {
                // Перечисление значений
                $filter[$fieldKey] = ['$in' => array_values($value)];
            }
        } else {
            throw new CException('Недопустимый тип значения для поиска.');
        }
        // Проекция: возвращаем только groupId
        $options = [
            'projection' => [
                'groupId' => 1,
                '_id' => 0 // Исключаем поле _id
            ]
        ];
        // Выполняем запрос и преобразуем результат в массив

        $cursor = $collection->find($filter, $options);
        // Преобразуем курсор в массив groupId
        $groupIds = [];
        foreach ($cursor as $doc) {
            $groupIds[] = $doc['groupId'];
        }

        return array_values($groupIds);
    }


    /*
     * Сначала получаем список groupId по условию и следом вытаскиваем все данные по этим groupId
     * линейным списком
     */
    public static function allDataOfListIDs($linkType, $IDs)
    {
        ini_set('memory_limit', '-1');

        //        $ids = [1,2,3];
        if ($IDs == []) {
            $data = Yii::app()->db->createCommand()->select('*')->from('SchemaData')->where('linkType="' . $linkType . '"')->leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                ->queryAll();
        } else {

            $data = Yii::app()->db->createCommand()->select('*')->from('SchemaData')->where('groupId in (' . implode(',', $IDs) . ') and linkType="' . $linkType . '"')->leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                ->queryAll();
        }

        return $data;
    }

    public static function packedDataByFieldValue($fieldId_value_array, $linkType, $schemaId)
    {
        $ids = null;
        foreach ($fieldId_value_array as $fieldName => $value) {

            $ids = self::equalList($fieldName, $schemaId, $value, $linkType, $ids);
        }

        $data = self::allDataOfListIDs($linkType, $ids);

        $pack = [];
        foreach ($data as $item) {
            if (!isset($pack[$item['fieldId']]))
                $pack[$item['fieldId']] = [];

            if (!isset($pack[$item['fieldId']][$item['value']])) {
                $pack[$item['fieldId']][$item['value']] = $item['groupId'];
            }
        }

        return $pack;
    }

    public static function forFilterChoiceData($fieldName, $schemaId = null)
    {
        $client = Yii::app()->mongoDb->getDb();
        $collection = $client->schemaData;

        // Выполняем distinct по полю fields.год.value
        $distinctData = $collection->distinct('fields.' . $fieldName . '.value');


        return $distinctData;
        Yii::import('cache.models.Cache');
        $cache = new Cache();
        if (!($result = $cache->getValue('forFilterChoiceData', $fieldName))) {

            $fieldmodel = SchemaField::getSchemaFieldByName($fieldName, $schemaId);
            $dataType = $fieldmodel->type;


            $typeTable = 'SchmType' . $dataType;
            $sql = "SELECT  sd.*,sts.value FROM SchemaData sd INNER JOIN $typeTable sts ON sd.id = sts.fieldDataId WHERE sd.fieldId = :fieldId";
            $data = Yii::app()->db->createCommand($sql)->queryAll(true, array(':fieldId' => $fieldmodel->id));
            $data = array_column($data, 'value');
            $data = array_unique($data);

            $cache->setValue('forFilterChoiceData', $fieldName, $data);
        } else {
            $data = $result;
        }


        return $data;

        //Достаем field по его имени, что бы понять его тип
        //Достаем distinct всех данных этого типа

    }

    public static function allOneLinkTypeList($linkType)
    {
        //        SchemaLinks::model()->findAllByAttributes([
        //            'linkType'=>$linkType
        //        ]);

        $data = Yii::app()->db->createCommand()->select('*')->from('SchemaLinks')->where('linkType="' . $linkType . '"')
            ->queryAll();
        return array_column($data, 'linkId');
    }
}
