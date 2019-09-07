<?php

class m20190907_010921_table_seo_tags extends Migrations
{
    public function up()
    {

        if ($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `seo_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE `seo_tags`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "таблица тегов для модуля seo";
    }

    public function isConfirmed($returnBoolean = false)
    {
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('seo_tags');
        if ($table){
            $result = true;
        } else {
            $result = false;
        }


        if ($returnBoolean) {
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