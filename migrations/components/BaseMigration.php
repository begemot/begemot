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

    public function addColumn($tableName,$columnName,$type='string'){
        return Yii::app()->db->createCommand()->addColumn($tableName,$columnName,  'string');
    }
    public function removeColumn($tableName,$columnName){
        return Yii::app()->db->createCommand()->dropColumn($tableName,$columnName);
    }
//    public function addColumn($tableName,$columnName){
//        return Yii::app()->db->createCommand()->addColumn($tableName,$columnName,  'string');
//    }


    private function getLocalVault(){
        $className = get_class($this);

        Yii::import('protected.modules.begemot.extensions.vault.FileVault');
        $vaultPath = Yii::getPathOfAlias('webroot.files.migraions');
        return new FileVault($vaultPath);
    }

    public function localStatusToggle(){
        $vault = $this->getLocalVault();
        $toggleVaultCollection = $vault->getCollection();

        $className = get_class($this);
        if (!isset($toggleVaultCollection[$className])){
            $toggleVaultCollection[$className] = true;
        }

        $toggleVaultCollection[$className] = !$toggleVaultCollection[$className];
        $vault->pushCollection($toggleVaultCollection);

        return $toggleVaultCollection[$className];
    }

    public function checkLocalStatus (){
        $className = get_class($this);
        $collection = $this->getLocalVault()->getCollection();
        if (!isset($collection[$className])){
            $collection[$className] = false;
        }
        return $collection[$className];
    }

}