<?php

class m141006_081712_create_slider extends Migrations
{
	public function up()
	{

		if($this->isConfirmed(true) == true) return false;

        $sql = "
         CREATE TABLE IF NOT EXISTS `slider` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `image` text NOT NULL,
           `header` text NOT NULL,
           `text1` text NOT NULL,
           `text2` text NOT NULL,
           `text3` text NOT NULL,
           `order` int(11) NOT NULL,
           PRIMARY KEY (`id`)
         ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $this->execute($sql);

        return true;
	}

	public function down()
	{
		if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `slider`;";
        $this->execute($sql);

        return true;
	}
   
	public function getDescription()
	{
		return "Добавление таблицы для слайдера.";
	}

	public function isConfirmed($returnBoolean = false){
		Yii::app()->db->schema->refresh();
		$table = Yii::app()->db->schema->getTable('slider');
		$result = isset($table->columns['id']);

        if($returnBoolean){
        	return $result;
        }

        return parent::confirmByWords($result);
	}
}