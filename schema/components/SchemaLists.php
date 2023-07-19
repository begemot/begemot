<?php

Yii::import('schema.models.*');
Yii::import('schema.models.types.*');

class SchemaLists
{
    public static function fromTolList(string $fieldName,$schemaId, string $linkType, array $groupIdList = [], $fromValue = null, $toValue = null): array
    {

        return self::getList($fieldName,$schemaId, ['from' => $fromValue, 'to' => $toValue], $linkType, $groupIdList);
    }

    public static function equalList(string $fieldName,$schemaId, $value, string $linkType, array $groupIdList = null): array
    {
        if (!is_array($value)) {
            $field = SchemaField::getSchemaFieldByName($fieldName,$schemaId);
            $result = self::getCahedEqualList($linkType, $field->id, $value);

            if (!$result) {
                $result = self::getList($fieldName,$schemaId, $value, $linkType);
                self::setCahedEqualList($linkType, $field->id, $value, $result);
            }

            if ($groupIdList === null) {
                return $result;
            } elseif (count($groupIdList) > 0) {
                return array_intersect($result, $groupIdList);
            } elseif (count($groupIdList) == 0) {
                return [];
            }

        } else {
            $resultArray = [];
            foreach ($value as $item){
                $resultArray = array_merge($resultArray,self::equalList( $fieldName,$schemaId,$item ,  $linkType,  $groupIdList));

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
    public static function getList(string $fieldName,$schemaId, $value, string $linkType, array $groupIdList = []): array
    {

//        Yii::import('cache.models.Cache');
//        $cache = new Cache();
//        $groupCacheKey = '';
//        if (count($groupIdList) > 0) {
//            $groupCacheKey = implode('_', $groupIdList);
//        }
        $valueCacheKey = '';
//        if (is_array($value)) {
//            if (isset($value['from']) || isset($value['to'])) {
//                $valueCacheKey = 'getList_from_' . $value['from'] . '_to_' . $value['to'];
//            } else
//                $valueCacheKey = 'getList_' . implode('_', $value);
//        } else {
//            $valueCacheKey = 'getList_' . $value;
//        }

//        print_r($fieldName.'_'.$valueCacheKey.'_'.$linkType.'_'.$groupCacheKey);
//        die();
//        if (!($result = $cache->getValue('SchemaLists.equalList', $fieldName . '_' . $valueCacheKey . '_' . $linkType . '_' . $groupCacheKey))) {
        $field = SchemaField::getSchemaFieldByName($fieldName,$schemaId);
        $type = $field->type;

        if (is_array($value)) {
            if (isset($value['from']) || isset($value['to'])) {
                $sqlFromPart = 'tb1.value > ' . $value['from'] . '';
                $sqlToPart = 'tb1.value < ' . $value['to'] . '';
                //$sqlPart = 'tb1.value > "' . $value['from'].'"';//. '" and tb1.value < "'.$value['to'].'"';

                if ($value['from'] != null && $value['to'] == null) {
                    $sqlPart = $sqlFromPart;
                }

                if ($value['from'] == null && $value['to'] != null) {
                    $sqlPart = $sqlToPart;
                }

                if ($value['from'] != null && $value['to'] != null) {
                    $sqlPart = $sqlFromPart . ' and ' . $sqlToPart;
                }
            } else {
                $value = array_map(function ($val) {
                    return '"' . $val . '"';
                }, $value);
                $sqlPart = 'tb1.value in (' . implode(',', $value) . ') ';
            }

        } else {
            $sqlPart = 'tb1.value="' . $value . '"';
        }

//            if ($type !== 'String') {
//                throw new Exception('Нужно сделать обработку других типов данных');
//            }

        $where = $sqlPart . ' and fieldId=' . $field->id . ' and linkType="' . $linkType . '"';

        if (!empty($groupIdList)) {
            $where .= ' and groupId in(' . implode(',', $groupIdList) . ')';
        }

        $data = Yii::app()->db->createCommand()
            ->select('groupId')
            ->from('SchemaData')
            ->where($where)
            ->leftJoin('SchmType' . $type . ' tb1', 'SchemaData.id=tb1.fieldDataId')
            ->queryAll();


//            $cache->setValue('SchemaLists.equalList', $fieldName . '_' . $valueCacheKey . '_' . $linkType . '_' . $groupCacheKey, $data);
//        } else {
//            $data = $result;
//        }


        return array_column($data, 'groupId');

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
            $data = Yii::app()->db->createCommand()->select('*')->
            from('SchemaData')->
            where('linkType="' . $linkType . '"')->
            leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                ->queryAll();
        } else {

            $data = Yii::app()->db->createCommand()->select('*')->
            from('SchemaData')->
            where('groupId in (' . implode( ',',$IDs) . ') and linkType="' . $linkType . '"')->
            leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                ->queryAll();
        }

        return $data;
//        $result = [];
//        foreach ($data as $line){
//            if (!isset($result[$line['groupId']]))
//                $result[$line['groupId']] =[];
//
//            $result[$line['groupId']][$line['fieldId']] = $line;
//        }

//        foreach ($result as $item) {
//
//        }
    }

    public static function packedDataByFieldValue($fieldId_value_array, $linkType,$schemaId)
    {
        $ids = null;
        foreach ($fieldId_value_array as $fieldName => $value) {

            $ids = self::equalList($fieldName,$schemaId, $value, $linkType, $ids);
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

    public static function forFilterChoiceData($fieldName,$schemaId = null)
    {

        Yii::import('cache.models.Cache');
        $cache = new Cache();
        if (!($result = $cache->getValue('forFilterChoiceData', $fieldName))) {

            $fieldmodel = SchemaField::getSchemaFieldByName($fieldName,$schemaId);
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

        $data = Yii::app()->db->createCommand()->select('*')->
        from('SchemaLinks')->
        where('linkType="' . $linkType . '"')
            ->queryAll();
        return array_column($data, 'linkId');
    }

}