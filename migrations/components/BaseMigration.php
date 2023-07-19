<?php


class BaseMigration
{

    public function execute($sql){
        $connection=Yii::app()->db;

        $command = $connection->createCommand($sql);

        $command->execute();
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
}