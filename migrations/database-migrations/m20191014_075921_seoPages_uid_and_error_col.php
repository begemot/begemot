<?php

class m20191014_075921_seoPages_uid_and_error_col extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `seo_pages`
	            ADD COLUMN `uid` varchar(45);
	            ALTER TABLE `seo_pages`
	            ADD COLUMN `checkError` varchar(45);
	            
	            ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "
    ALTER TABLE `seo_pages`
	  DROP COLUMN `uid`;
    ALTER TABLE `seo_pages`
	  DROP COLUMN `checkError`;	
	";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "seoPages uid и ошибки";
    }

    public function isConfirmed($returnBoolean = false){

        $result = $this->columnExist('seo_pages','uid');
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