<?php
/*
 * Базовый класс модели, для удобной работы с набором схем и полей в виде одной сущности.
 *
 * Для работы используем CSchemaLink, как более низкий уровень. Этот класс считаем более высоким уровнем абстракции,
 * что бы можно было работать с набором данных сцены как с отдельной сущностью на манер ActiveRecord модели
 */

class CSchemaModel
{
//  Эти два парамера переопределяем при наследовании класса
    protected $schemaId = null;
    protected $linkType = null;
    protected $groupId = null;

    public $CSchemaLink = null;

    public function __construct($id)
    {
        if (is_null($this->schemaId) || is_null($this->linkType)){
            throw new Exception();
        }

        $this->groupId = $id;
        $this->CSchemaLink =  new CSchemaLink($this->linkType,$this->groupId,$this->schemaId);
    }

    public function set ($fieldId, $value, $type='String'){
        $this->CSchemaLink->set($fieldId, $value,$this->linkType, $type);
    }
    public function get ($fieldId){
       return  $this->CSchemaLink->get($fieldId);
    }

}