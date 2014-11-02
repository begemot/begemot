<?php

class m141006_081509_create_reviews extends Migrations
{
	public function up()
	{

		if($this->isConfirmed(true) == true) return false;

        $sql = "
         CREATE TABLE IF NOT EXISTS `reviews` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `pid` int(11) NOT NULL,
           `type` int(11) NOT NULL,
           `name` text NOT NULL,
           `email` text NOT NULL,
           `phone` text NOT NULL,
           `pluses` text NOT NULL,
           `cons` text NOT NULL,
           `general` text NOT NULL,
           `status` int(11) NOT NULL,
           `created_at` datetime NOT NULL,
           PRIMARY KEY (`id`)
         ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $this->execute($sql);

        return true;
	}

	public function down()
	{
		if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `reviews`;";
        $this->execute($sql);

        return true;
	}
   
	public function getDescription()
	{
		return "Добавление таблицы для отзывов.";
	}

	public function isConfirmed($returnBoolean = false){
		Yii::app()->db->schema->refresh();
		$table = Yii::app()->db->schema->getTable('reviews');
		$result = isset($table->columns['id']);

        if($returnBoolean){
        	return $result;
        }

        return parent::confirmByWords($result);
	}
}