<?php

class m20160817_122629_catalog_colors extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `catColors` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(45) DEFAULT NULL,
                  `colorCode` varchar(45) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ";
        $this->execute($sql);

        $sql = "CREATE TABLE `catColorsToCatItem` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `colorId` int(11) DEFAULT NULL,
                  `catItemId` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
                ";

        $this->execute($sql);
        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `catColors`;";
        $this->execute($sql);
        $sql = "DROP TABLE `catColorsToCatItem`;";
        $this->execute($sql);
        return true;
    }

    public function getDescription()
    {
        return "Таблицы для цветов в каталоге";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table1 = !is_null(Yii::app()->db->schema->getTable('catColors'));
        $table2 = !is_null(Yii::app()->db->schema->getTable('catColorsToCatItem'));
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