<?php

class m17000000_081713_catItem_name_255characters extends Migrations
{
  public function up()
  {
    if($this->isConfirmed(true) == true) return false;
    
    $sql = "ALTER TABLE `catItems`
    MODIFY name VARCHAR(255),
    MODIFY name_t VARCHAR(255)";
    $this->execute($sql);
    return true;
  }

  public function down()
  {
      if($this->isConfirmed(true) == false) return false;

      echo "m17000000_081713_catItem_name_255characters does not support migration down.\n";
      return false;
  }
  public function getDescription()
  {
    return "Изменение максимального значения на 255 полей 'name' и 'name_t' в таблице catItems";
  }


  public function isConfirmed($returnBoolean = false){
    $table = Yii::app()->db->schema->getTable('catItems');
    $result = isset($table->columns['maxSale']);

    $result = false;
    if($table){
        $tableData = Yii::app()->db->createCommand("select CHARACTER_MAXIMUM_LENGTH  from information_schema.columns where table_schema = DATABASE() AND table_name = 'catItems' AND COLUMN_NAME = 'name'")->queryRow();
        $result = ($tableData['CHARACTER_MAXIMUM_LENGTH'] == 255) ? true : false;
        if(!$tableData){
            $result = true;
        }
    }

    if($returnBoolean){
      return $result;
    }

    return parent::confirmByWords($result);
  }
}