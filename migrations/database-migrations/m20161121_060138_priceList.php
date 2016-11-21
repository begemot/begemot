<?php

class m20161121_060138_priceList extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "

CREATE TABLE `prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL COMMENT 'Название позиции',
  `catId` int(11) DEFAULT NULL COMMENT 'Катеогория позиции',
  `price` int(11) DEFAULT NULL COMMENT 'Цена',
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8;

CREATE TABLE `priceCats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;



";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;


        $sql = "
        DROP TABLE `prices`;
        DROP TABLE `priceCats`;


        ";
        $this->execute($sql);



        return true;
    }

    public function getDescription()
    {
        return "Структура БД для модуля Цен";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table1 = !is_null(Yii::app()->db->schema->getTable('prices'));
        $table2 = !is_null(Yii::app()->db->schema->getTable('priceCats'));
        $result = $table1 && $table2;
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