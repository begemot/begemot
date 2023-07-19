<?php

class m17000000_081715_parsers_stock_changes extends Migrations
{
  public function up()
  {
    if($this->isConfirmed(true) == true) return false;
    
      $sql = "ALTER TABLE `parsers_stock`
        ADD `images` text COLLATE 'utf8_general_ci' NOT NULL,
        ADD `parents` text COLLATE 'utf8_general_ci' NOT NULL,
        ADD `groups` text COLLATE 'utf8_general_ci' NOT NULL;";
      $this->execute($sql);
      return true;
  }

  public function down()
  {
    if ($this->isConfirmed(true) == false) return false;

    $sql = "ALTER TABLE `parsers_stock` DROP `images`, DROP `parents`, DROP `groups`;";
    $this->execute($sql);
    return true;
  }

  public function getDescription()
  {
    return "Добавление полей 'images', 'parents', 'groups' в таблицу parsers_stock";
  }


  public function isConfirmed($returnBoolean = false){
    $table = Yii::app()->db->schema->getTable('parsers_stock');
    $result = isset($table->columns['images']) && isset($table->columns['parents']) && isset($table->columns['groups']);

    if($returnBoolean){
      return $result;
    }

    return parent::confirmByWords($result);
  }
}