<?php

class m141002_083850_create_faq extends Migrations
{
	public function up()
	{

		if($this->isConfirmed(true) == true) return false;

        $sql = "
         CREATE TABLE IF NOT EXISTS `faq` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `name` text NOT NULL,
           `email` text NOT NULL,
           `phone` text NOT NULL,
           `question` text NOT NULL,
           `answer` text NOT NULL,
           `answered` tinyint(1) NOT NULL,
           `published` tinyint(1) NOT NULL,
           `create_at` datetime NOT NULL,
           `cid` int(11) NOT NULL,
           `order` int(11) NOT NULL,
           PRIMARY KEY (`id`)
         ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
         CREATE TABLE IF NOT EXISTS `faq_cats` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `name` text NOT NULL,
           PRIMARY KEY (`id`)
         ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $this->execute($sql);

        return true;
	}

	public function down()
	{
		if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `faq`;DROP TABLE `faq_cats`";
        $this->execute($sql);

        return true;
	}
   
	public function getDescription()
	{
		return "Добавление таблиц для вопрос-ответ и его разделов.";
	}

	public function isConfirmed($returnBoolean = false){
		Yii::app()->db->schema->refresh();
		$table = Yii::app()->db->schema->getTable('faq');
		$table2 = Yii::app()->db->schema->getTable('faq_cats');
		$result = isset($table->columns['id']) AND isset($table2->columns['id']);

        if($returnBoolean){
        	return $result;
        }

        return parent::confirmByWords($result);
	}
   
	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}