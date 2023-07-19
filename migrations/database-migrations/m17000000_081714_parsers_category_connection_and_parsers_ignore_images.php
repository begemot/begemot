<?php

class m17000000_081714_parsers_category_connection_and_parsers_ignore_images extends Migrations
{
  public function up()
  {
    if($this->isConfirmed(true) == true) return false;
    
      $sql = "
      CREATE TABLE `parsers_category_connection` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `connect_name` varchar(255) NOT NULL,
        `category_id` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

      CREATE TABLE `parsers_ignore_images` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `md5` varchar(255) COLLATE utf8_bin NOT NULL,
        `sha1` varchar(255) COLLATE utf8_bin NOT NULL,
        `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `md5` (`md5`),
        UNIQUE KEY `sha1` (`sha1`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
      $this->execute($sql);
      return true;
  }

  public function down()
  {
      if ($this->isConfirmed(true) == false) return false;

      $sql = "DROP TABLE IF EXISTS `parsers_category_connection`;
      DROP TABLE IF EXISTS `parsers_ignore_images`;";
      $this->execute($sql);
      return true;
  }

  public function getDescription()
  {
    return "Создание таблиц parsers_category_connection и parsers_ignore_images'";
  }


  public function isConfirmed($returnBoolean = false){
    $parsers_category_connection = Yii::app()->db->schema->getTable('parsers_category_connection');
    $parsers_ignore_images = Yii::app()->db->schema->getTable('parsers_ignore_images');
    $result = isset($parsers_category_connection) && isset($parsers_ignore_images);

    if($returnBoolean){
      return $result;
    }

    return parent::confirmByWords($result);
  }
}