<?php

class m20180313_025359_catShipment_table extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `catShipment` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(45) DEFAULT NULL,
                  `price` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
                ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `catShipment`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Таблица для доставки";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table1 = !is_null(Yii::app()->db->schema->getTable('catShipment'));
        $result = $table1 ;
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