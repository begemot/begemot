<?php


class FieldTypesChanger
{
    public static function stringToInt($fieldId){

        $schemaField = SchemaField::model()->findByPk($fieldId);
        $schemaFieldType = $schemaField->type;

        //$schemaField->type = 'int';
        //$schemaField->save();

        $schemaDataArray = SchemaData::model()->findAllByAttributes(['fieldId'=>$schemaField->id]);
        //echo count($schemaDataArray);
        foreach ($schemaDataArray as $schemaData ){
            /** @var $schemaData SchemaData*/

//            print_r($schemaData);
            print_r($schemaData->value);
            $oldValue = $schemaData->value;
//            $schemaData->fieldType = 'Int';
            $schemaData->setData($oldValue,'Int');

        }
    }
}