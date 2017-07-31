<?php

class m20160609_061831_webParserDownload_table extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `webParserDownload` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `processId` int(11) DEFAULT NULL,
      `fileUrl` varchar(500) DEFAULT NULL,
      `fieldId` varchar(500) DEFAULT NULL,
      `file` varchar(500) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=215090 DEFAULT CHARSET=utf8;
    ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `webParserDownload`;;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Создание таблицы webParserDownload";
    }

    public function isConfirmed($returnBoolean = false){

        Yii::app()->db->schema->refresh();
        $table1 = Yii::app()->db->schema->getTable('webParserDownload');
        $result = isset($table1->columns['id']);



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