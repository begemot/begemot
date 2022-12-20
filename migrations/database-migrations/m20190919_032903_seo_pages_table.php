<?php

class m20190919_032903_seo_pages_table extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE IF NOT EXISTS `seo_pages` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `url` varchar(500) DEFAULT NULL,
          `title` varchar(500) DEFAULT NULL,
          `content` longtext,
          `status` int(11) DEFAULT NULL,
          `tagsCoputedFlag` int(11) DEFAULT '0',
          `contentHash` varchar(200) DEFAULT NULL,
          `mime` varchar(200) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `Index 2` (`url`(255))
        ) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;
        ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `seo_pages`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Создание таблицы seo_pages для модуля СЕО";
    }

    public function isConfirmed($returnBoolean = false){

        $result =$this->tableExist('seo_pages');

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