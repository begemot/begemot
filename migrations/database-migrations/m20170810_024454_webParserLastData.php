<?php

class m20170810_024454_webParserLastData extends Migrations
{
    public function up()
    {

        if ($this->isConfirmed(true) == true) return false;

        $sql = "
            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `lastParserProcId` AS select max(`webParser`.`id`) AS `maxId` from `webParser`;

            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `lastWebParserData` AS select `webParserData`.`id` AS `id`,`webParserData`.`processId` AS `processId`,`webParserData`.`fieldName` AS `fieldName`,`webParserData`.`fieldId` AS `fieldId`,`webParserData`.`fieldData` AS `fieldData`,`webParserData`.`parentDataId` AS `parentDataId`,`webParserData`.`sourcePageUrl` AS `sourcePageUrl`,`webParserData`.`fieldParentId` AS `fieldParentId`,`webParserData`.`fieldGroupId` AS `fieldGroupId`,`webParserData`.`fieldModifId` AS `fieldModifId` from `webParserData` where `webParserData`.`processId` in (select `lastParserProcId`.`maxId` from `lastParserProcId`);

            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `lastWebParserDownload` AS select `webParserDownload`.`id` AS `id`,`webParserDownload`.`processId` AS `processId`,`webParserDownload`.`fileUrl` AS `fileUrl`,`webParserDownload`.`fieldId` AS `fieldId`,`webParserDownload`.`file` AS `file` from `webParserDownload` where `webParserDownload`.`processId` in (select `lastParserProcId`.`maxId` from `lastParserProcId`);


            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `lastWebParserPages` AS select `webParserPage`.`id` AS `id`,`webParserPage`.`procId` AS `procId`,`webParserPage`.`content` AS `content`,`webParserPage`.`url` AS `url`,`webParserPage`.`content_hash` AS `content_hash`,`webParserPage`.`url_hash` AS `url_hash`,`webParserPage`.`http_code` AS `http_code`,`webParserPage`.`mime` AS `mime` from `webParserPage` where `webParserPage`.`procId` in (select `lastParserProcId`.`maxId` from `lastParserProcId`);

            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `lastWebParserPages` AS select `webParserPage`.`id` AS `id`,`webParserPage`.`procId` AS `procId`,`webParserPage`.`content` AS `content`,`webParserPage`.`url` AS `url`,`webParserPage`.`content_hash` AS `content_hash`,`webParserPage`.`url_hash` AS `url_hash`,`webParserPage`.`http_code` AS `http_code`,`webParserPage`.`mime` AS `mime` from `webParserPage` where `webParserPage`.`procId` in (select `lastParserProcId`.`maxId` from `lastParserProcId`);

            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `lastWebParserProcTasks` AS select `webParserScenarioTask`.`id` AS `id`,`rosvesdehod`.`webParserScenarioTask`.`processId` AS `processId`,`rosvesdehod`.`webParserScenarioTask`.`scenarioName` AS `scenarioName`,`rosvesdehod`.`webParserScenarioTask`.`target_id` AS `target_id`,`rosvesdehod`.`webParserScenarioTask`.`taskStatus` AS `taskStatus`,`rosvesdehod`.`webParserScenarioTask`.`taskType` AS `taskType`,`rosvesdehod`.`webParserScenarioTask`.`target_type` AS `target_type` from `webParserScenarioTask` where `rosvesdehod`.`webParserScenarioTask`.`processId` in (select `lastParserProcId`.`maxId` from `lastParserProcId`);


        ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "
            DROP VIEW `lastParserProcId`;
            DROP VIEW `lastWebParserData`;
            DROP VIEW `lastWebParserDownload`;
            DROP VIEW `lastWebParserPages`;
            DROP VIEW `lastWebParserPages`;
            DROP VIEW `webParserScenarioTask`;
        ";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Представления для отображения данных последнего процесса webParser";
    }

    public function isConfirmed($returnBoolean = false)
    {
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('lastParserProcId');
        $result = isset($table->columns['maxId']);

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