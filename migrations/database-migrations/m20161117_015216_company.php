<?php

class m20161117_015216_company extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `companyDepart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `text` mediumtext,
  `titleSeo` varchar(200) DEFAULT NULL,
  `nameT` varchar(200) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

CREATE TABLE `companyEmployee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `position` varchar(200) DEFAULT NULL,
  `text` mediumtext,
  `order` int(11) DEFAULT NULL,
  `nameT` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

CREATE TABLE `companyEmpToDep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empId` int(11) DEFAULT NULL,
  `depId` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "
        DROP TABLE `companyEmployee`;
        DROP TABLE `companyDepart`;
        DROP TABLE `companyEmpToDep`;


        ";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Структура БД модуля структуры компании";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table1 = !is_null(Yii::app()->db->schema->getTable('companyDepart'));
        $table2 = !is_null(Yii::app()->db->schema->getTable('companyEmployee'));
        $table3 = !is_null(Yii::app()->db->schema->getTable('companyEmpToDep'));
        $result = $table1 && $table2 && $table3;
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