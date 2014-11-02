<?php

class m130818_093534_parsers_tables extends Migrations
{
	public function up()
	{

		if($this->isConfirmed(true) == true) return false;

        $sql = "
        CREATE TABLE IF NOT EXISTS `parsers_linking` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `fromId` varchar(255) NOT NULL,
		  `toId` int(11) NOT NULL,
		  `filename` varchar(100) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
        CREATE TABLE IF NOT EXISTS `parsers_stock` (
		  `id` varchar(100) COLLATE utf8_bin NOT NULL,
		  `price` int(15) NOT NULL,
		  `name` varchar(100) COLLATE utf8_bin NOT NULL,
		  `text` text COLLATE utf8_bin NOT NULL,
		  `quantity` int(11) NOT NULL,
		  `filename` varchar(100) COLLATE utf8_bin NOT NULL,
		  `linked` int(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
        $this->execute($sql);

        return true;
	}

	public function down()
	{
		if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `parsers_stock`;DROP TABLE `parsers_linking`";
        $this->execute($sql);

        return true;
	}

	public function getDescription()
	{
		return "Добавление таблиц для кешированния данных с парсинга и добавление поля 'filename' для parsers_linking, а так же переименование таблицы parsers_stock";
	}

	public function isConfirmed($returnBoolean = false){
		Yii::app()->db->schema->refresh();
		$table = Yii::app()->db->schema->getTable('parsers_stock');
		$table2 = Yii::app()->db->schema->getTable('parsers_linking');
		$result = isset($table->columns['id']) AND isset($table->columns['id']);

        if($returnBoolean){
        	return $result;
        }

        return parent::confirmByWords($result);
	}
}