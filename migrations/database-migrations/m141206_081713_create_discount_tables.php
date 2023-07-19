<?php

class m141206_081713_create_discount_tables extends Migrations
{
	public function up()
	{

		if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `discount` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(100) DEFAULT NULL,
              `sale` int(11) DEFAULT NULL,
              `active` int(11) DEFAULT '0',
              `pub_date` int(10) NOT NULL,
              `create_time` int(10) NOT NULL,
              `update_time` int(10) NOT NULL,
              `order` int(10) NOT NULL,
              `authorId` int(10) NOT NULL,
              `top` int(10) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
            ";
       $this->execute($sql);


        $sql = "CREATE TABLE `discountRelation` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `discountId` int(11) DEFAULT NULL,
                  `type` int(11) DEFAULT NULL,
                  `targetId` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
                ";
        $this->execute($sql);

        return true;
	}

	public function down()
	{
		if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `discount`";
        $this->execute($sql);

        $sql = "DROP TABLE `discountRelation`";
        $this->execute($sql);

        return true;
	}

	public function getDescription()
	{
		return "Добавление таблиц для скидок";
	}

	public function isConfirmed($returnBoolean = false){


		Yii::app()->db->schema->refresh();
		$table1 = Yii::app()->db->schema->getTable('discount');
		$result1 = isset($table1->columns['id']);

        $table2 = Yii::app()->db->schema->getTable('discountRelation');
		$result2 = isset($table2->columns['id']);

        $result = $result1 and $result2;

        if($returnBoolean){
        	return $result;
        }

        return parent::confirmByWords($result);
	}

}