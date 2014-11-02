<?php

class m130818_093532_add_stock_in_and_date_of_delivery_and_article extends Migrations
{
	public function up()
	{
		if($this->isConfirmed(true) == true) return false;
		
        $sql = "ALTER TABLE  `catItems` ADD  `quantity` INT NOT NULL DEFAULT '0' , ADD  `delivery_date` INT NOT NULL, ADD  `article` VARCHAR(100) NOT NULL";
        return $this->execute($sql);
	}

	public function down()
	{
		if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catItems`
	DROP COLUMN `quantity`, DROP COLUMN `delivery_date`, DROP COLUMN `article`";
        $this->execute($sql);

        return true;
	}

	public function getDescription()
	{
		return "Добавление полей в таблицу(количество на складе, дата поставки и артикль)";
	}


	public function isConfirmed($returnBoolean = false){
		$table = Yii::app()->db->schema->getTable('catItems');
		$result = (isset($table->columns['quantity']) AND isset($table->columns['delivery_date']) AND isset($table->columns['article'])) ? true : false;


        if($returnBoolean){
        	return $result;
        }

        return parent::confirmByWords($result);
	}
}