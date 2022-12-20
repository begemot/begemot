<?php

class m20180313_032514_catOrder extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "

            CREATE TABLE `catOrder` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(200) DEFAULT NULL,
              `phone` varchar(45) DEFAULT NULL,
              `mail` varchar(45) DEFAULT NULL,
              `information` mediumtext,
              `shipmentId` int(11) DEFAULT NULL,
              `itemIdArray` mediumtext,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

            CREATE TABLE `catOrderItems` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `orderId` int(11) DEFAULT NULL,
              `itemId` int(11) DEFAULT NULL,
              `count` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


	";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "
        DROP TABLE `catOrder`;
        DROP TABLE `catOrderItems`;

        ";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Таблицы заказа.";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table1 = !is_null(Yii::app()->db->schema->getTable('catOrder'));
        $table2 = !is_null(Yii::app()->db->schema->getTable('catOrderItems'));

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