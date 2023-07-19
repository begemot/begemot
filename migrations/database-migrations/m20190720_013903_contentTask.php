<?php

class m20190720_013903_contentTask extends Migrations
{
    public function up()
    {

        if($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE `ContentTask` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(45) DEFAULT NULL,
                  `text` mediumtext,
                  `type` varchar(45) DEFAULT NULL,
                  `actionsList` mediumtext,
                  `dataElementsList` mediumtext,
                  `accessCode` varchar(45) DEFAULT NULL,
                  `codeDate` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
                
                
                CREATE TABLE `ContentTaskAdded` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `type` varchar(45) DEFAULT NULL,
                  `taskId` int(11) DEFAULT NULL,
                  `contentId` int(11) DEFAULT NULL,
                  `iteration` int(11) DEFAULT '0',
                  `status` varchar(45) DEFAULT 'new',
                  `new` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;
                
                
                CREATE TABLE `ContentTaskData` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `dataType` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
                  `name` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
                  `param1` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
                  `param2` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
                  `param3` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
                  `data` mediumtext,
                  `groupId` int(11) DEFAULT NULL,
                  `taskId` int(11) DEFAULT NULL,
                  `subTaskId` int(11) DEFAULT NULL,
                  `iteration` int(11) DEFAULT '0',
                  `isBaseData` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=641 DEFAULT CHARSET=utf8;

                CREATE TABLE `ContentTaskReviewData` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `subtaskId` int(11) DEFAULT NULL,
                  `iteration` int(11) DEFAULT NULL,
                  `jsonData` text,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;


                
";
        if ($this->execute($sql)) {

            return true;
        } else {
            return false;
        }

    }

    public function down()
    {
        if($this->isConfirmed(true) == false) return false;

        $sql = "
            DROP TABLE IF EXISTS `ContentTask`;
            DROP TABLE IF EXISTS `ContentTaskAdded`;
            DROP TABLE IF EXISTS `ContentTaskData`;
            DROP TABLE IF EXISTS `ContentTaskReviewData`;
        
        
        ";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Модуль управления контентом основные таблицы";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();


        if(!is_null(Yii::app()->db->schema->getTable('ContentTask'))){
            $result = true;
        } else $result = false;
        if($returnBoolean){return $result;}else
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