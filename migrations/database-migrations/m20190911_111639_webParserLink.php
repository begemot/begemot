<?php

class m20190911_111639_webParserLink extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `webParserLink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sourceUrl` varchar(700) NOT NULL,
  `url` varchar(700) NOT NULL,
  `procId` int(11) NOT NULL,
  `anchor` varchar(500) DEFAULT NULL,
  `export` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73472 DEFAULT CHARSET=utf8;
;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "drop table `webParserLink`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Таблица со ссылками в парсере";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();

        $table = Yii::app()->db->schema->getTable('webParserLink');
        if ($table){
            $result = true;
        } else {
            $result = false;
        }

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