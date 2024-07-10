<?php
/*
 * Базовый класс модели, для удобной работы с набором схем и полей в виде одной сущности.
 *
 * Для работы используем CSchemaLink, как более низкий уровень. Этот класс считаем более высоким уровнем абстракции,
 * что бы можно было работать с набором данных сцены как с отдельной сущностью на манер ActiveRecord модели
 */
Yii::import('schema.models.SchemaLinks');
Yii::import('schema.components.CSchemaLink');

class CSchemaModel
{

    //  Эти два парамера переопределяем при наследовании класса
    public static $schemaId = 'no data';
    protected $linkType = null;
    protected $groupId = null;

    public $schemaLink = null;



    public function __construct($id = null, $schemaField = null, $fieldValue = null,  $linkType = null)
    {

        if (is_null(static::$schemaId) || is_null($this->linkType)) {
            throw new Exception();
        }

        if (is_null($id) && !is_null($schemaField) && !is_null($fieldValue)) {
            $foundGroupIds = self::findAllGroupIdByAttribute($schemaField, $fieldValue,$linkType);
            if (count($foundGroupIds) > 1) throw new Exception('Вернуло больше одного значения. Таким методом создавать CSchemaModel можно только когда значение уникально.');
            if (count($foundGroupIds) == 1) {
                $id = array_shift($foundGroupIds);
            }
        }

        // если id не передано, то ищем максимальный id и делаем +1
        if (is_null($id)) {
            $id = CSchemaLink::getMaxId($this->linkType) + 1;
            if (!$id) $id = 1;
        }

        $this->groupId = $id;
        $this->schemaLink = new CSchemaLink($this->linkType, $this->groupId, static::$schemaId, $fieldValue);
    }





    public function set($fieldId, $value, $type = 'String')
    {
        $this->schemaLink->set($fieldId, $value, $type);
    }

    public function get($fieldId)
    {
        return $this->schemaLink->get($fieldId);
    }

    public static function findAll()
    {
        Yii::import('schema.models.SchemaLinks');
        //      echo self::$test;
        //  return SchemaLinks::model()->findAllByAttributes([]);

    }

    public static function findAllGroupIdByAttribute($fieldName, $fieldValue,$linkType = null)
    {
        Yii::import('schema.models.SchemaLinks');
        Yii::import('schema.models.SchemaField');

        $schemaId =  static::$schemaId;

        $schemaFieldModel = SchemaField::getSchemaFieldByName($fieldName, $schemaId);
        $fieldId = $schemaFieldModel->id;
        $sql = "
    select 
        * 
    from 
        SchemaData 
            join SchmTypeString 
                on SchemaData.id=SchmTypeString.fieldDataId 
    where 
        SchemaData.fieldType ='String' and 
        fieldId='" . $fieldId . "'".(
            !is_null($linkType)?' and linkType=\''.$linkType.'\'':''
        )."
        and value='" . $fieldValue . "';";

        $command = Yii::app()->db->createCommand($sql);
        $result = $command->queryAll();

        return array_unique(array_column($result, 'groupId'));
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}