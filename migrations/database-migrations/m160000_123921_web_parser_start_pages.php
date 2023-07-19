<?php

class m160000_123921_web_parser_start_pages extends Migrations
{
	public function up()
	{

		if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `webParser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT NULL,
  `report_text` mediumtext,
  `processTime` timestamp NULL DEFAULT NULL,
  `pagesProcessed` int(11) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

CREATE TABLE `webParserData` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `processId` int(11) DEFAULT NULL,
  `fieldName` varchar(45) DEFAULT NULL,
  `fieldId` varchar(500) DEFAULT NULL,
  `fieldData` longtext,
  `parentDataId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=utf8;

CREATE TABLE `webParserPage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `procId` int(11) DEFAULT NULL,
  `content` longtext,
  `url` varchar(700) DEFAULT NULL,
  `content_hash` varchar(32) DEFAULT NULL,
  `url_hash` varchar(32) DEFAULT NULL,
  `http_code` int(11) DEFAULT NULL,
  `mime` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_hash_index` (`content_hash`),
  KEY `url_hash_index` (`url_hash`),
  KEY `url_index` (`url`(255))
) ENGINE=InnoDB AUTO_INCREMENT=778 DEFAULT CHARSET=utf8;

CREATE TABLE `webParserScenarioTask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `processId` int(11) DEFAULT NULL,
  `scenarioName` varchar(45) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `taskStatus` varchar(45) DEFAULT NULL,
  `taskType` varchar(45) DEFAULT NULL,
  `target_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2534 DEFAULT CHARSET=utf8;

CREATE TABLE `webParserUrl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(700) NOT NULL,
  `procId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2010 DEFAULT CHARSET=utf8;";
        $this->execute($sql);

        return true;
	}

	public function down()
	{
		if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `webParserUrl`;
				DROP TABLE `webParserScenarioTask`;
				DROP TABLE `webParserPage`;
				DROP TABLE `webParserData`;
				DROP TABLE `webParser`;";
        $this->execute($sql);

        return true;
	}

	public function getDescription()
	{
		return "Базовые таблицы для парсера.";
	}

	public function isConfirmed($returnBoolean = false){
		Yii::app()->db->schema->refresh();
		$table = Yii::app()->db->schema->getTable('webParserUrl');
		$result = isset($table->columns['id']);

        if($returnBoolean){
        	return $result;
        }

        return parent::confirmByWords($result);
	}

	/*
	 * ALTER TABLE `catItems`
	DROP COLUMN `top`;
	 *
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}