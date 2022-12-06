<?php

Yii::import('schema.models.*');
Yii::import('schema.models.types.*');

class SchemaLists
{

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
    public static function equalList($fieldName, $value, $linkType, $groupIdList = [])
    {
        $field = SchemaField::getSchemaFieldByName($fieldName);
        $type = $field->type;

        if (is_array($value)) {
            foreach ($value as $key=>$val){
                $value[$key] = '"'.$val.'"';
            }
            $sqlPart = implode(',',$value);
            $sqlPart = 'tb1.value in ('.$sqlPart.') ';
        } else {
            $sqlPart = 'tb1.value="' . $value . '"';
        }


        if ($type = 'String') {

            if (is_array($groupIdList) && count($groupIdList) > 0) {
                $where = $sqlPart.' and fieldId=' . $field->id . ' and linkType="' . $linkType . '" and groupId in(' . implode(',', $groupIdList) . ')';
            } else {
                $where = $sqlPart.'  and fieldId=' . $field->id . ' and linkType="' . $linkType . '"';
            }


            $data = Yii::app()->db->createCommand()->select('*')->
            from('SchemaData')->
            where($where)->
            leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                ->queryAll();


            return array_column($data, 'groupId');
        } else {
            throw new Exception('Нужно сделать обработку других типов данных');
        }

    }

    /*
     * Сначала получаем список groupId по условию и следом вытаскиваем все данные по этим groupId
     * линейным списком
     */
    public static function allDataOfListIDs($fieldName, $value, $linkType, $groupIdList = [])
    {
        ini_set('memory_limit', '-1');
        $IDS = self::equalList($fieldName, $value, $linkType, $groupIdList);

//        $ids = [1,2,3];
        if ($IDS == []) {
            $data = Yii::app()->db->createCommand()->select('*')->
            from('SchemaData')->
            where('linkType="' . $linkType . '"')->
            leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                ->queryAll();
        } else {
            $data = Yii::app()->db->createCommand()->select('*')->
            from('SchemaData')->
            where('groupId in (' . implode($IDS, ',') . ') and linkType="' . $linkType . '"')->
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

    public static function packedDataByFieldValue($fieldName, $value, $linkType)
    {
        $data = self::allDataOfListIDs($fieldName, $value, $linkType);

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

    public static function forFilterChoiceData($fieldName)
    {

        $fieldmodel = SchemaField::getSchemaFieldByName($fieldName);
        $dataType = $fieldmodel->type;
        if ($dataType == 'String') {
            $data = Yii::app()->db->createCommand()->selectDistinct('value')->
            from('SchemaData')->
            where('fieldId="' . $fieldmodel->id . '"')->
            leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                ->queryAll();
        }

        return array_column($data, 'value');

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