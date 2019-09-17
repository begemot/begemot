<?php

class m20190913_043409_seo_pages_longtext extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "
    ALTER TABLE `seo_pages`
	      DROP COLUMN `content`;

ALTER TABLE `seo_pages`
	ADD COLUMN `content` LONGTEXT;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `seo_pages`
	      DROP COLUMN `content`;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "такие дела";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('seo_pages');
      
        $result = $table->columns['content']->dbType=='longtext';

        if($returnBoolean){
            return $result;
        }

        return parent::confirmByWords($result);
    }
}