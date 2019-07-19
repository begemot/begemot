<?php

/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 20.12.2018
 * Time: 18:37
 */
class CatalogGroup extends BaseDataType
{
    public $title = 'Каталог - категории';

    public $tableName = 'catCategory';
    public $tableFieldTitle = 'name';

    public $actions = [
        ['id' => 'edit'],
        ['id' => 'create']
    ];

     public  function getDataFields()
    {
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catCategory');
        $result = $table->columns;
        unset($result['id']);

        $resultArray = [];

        foreach ($result as $rowKey=>$rowMeta){
            $resultArray[$rowKey]['name']=$rowMeta->name;
        }

        return $resultArray;
    }
}