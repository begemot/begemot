<?php

class m141206_081714_add_maxSale_minSale_to_discount_tables extends Migrations
{
  public function up()
  {
    if($this->isConfirmed(true) == true) return false;
    
      $sql = "ALTER TABLE `discount`
      ADD COLUMN `maxSale` INT(11) DEFAULT '0' NULL,
      ADD COLUMN `minSale` INT(11) DEFAULT '0' NULL;";
      return $this->execute($sql);
  }

  public function down()
  {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `discount`
        DROP COLUMN `maxSale`,
        DROP COLUMN `minSale`;";
        return $this->execute($sql);
  }


  public function isConfirmed($returnBoolean = false){
    $table = Yii::app()->db->schema->getTable('discount');
    $result = isset($table->columns['maxSale']);

    if($returnBoolean){
      return $result;
    }

    return parent::confirmByWords($result);
  }
}