<?php

class Migrations
{
    public function isConfirmed($returnBoolean = false)
    {
        return "Неизвестно";
    }

    public function getDescription()
    {
        return "Нету описания";
    }

    public function up()
    {
        return false;
    }

    public function down()
    {
        return false;
    }

    public function execute($sql)
    {
        return Yii::app()->db->createCommand($sql)->execute();
    }

    public function confirmByWords($return)
    {
        if ($return == true) {
            return "Применена";
        } else {
            return "Еще не применялась";
        }

    }

    public function tableExist($tableName)
    {
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable($tableName);
        if ($table) {
            return true;
        } else
            return false;
    }

    public function columnExist($tableName,$columnName){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable($tableName);
        return $result = isset($table->columns[$columnName]);
    }

    public function addColumn(){

    }


}