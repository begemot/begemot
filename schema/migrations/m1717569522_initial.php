<?php

class m1717569522_initial extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true) == true) return false;

        $sql = <<<SQL
CREATE TABLE `Schema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `catId` int(11) DEFAULT NULL,
  `catArticul` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=915 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SchemaData` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) DEFAULT NULL,
  `groupId` int(11) DEFAULT NULL,
  `fieldType` varchar(100) DEFAULT NULL,
  `valueId` varchar(100) DEFAULT NULL,
  `schemaId` int(11) DEFAULT NULL,
  `linkType` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SchemaData_fieldId_IDX` (`fieldId`,`groupId`,`schemaId`) USING BTREE,
  KEY `SchemaData_id_IDX` (`id`) USING BTREE,
  KEY `SchemaData_groupId_IDX` (`groupId`) USING BTREE,
  KEY `SchemaData_fieldType_IDX` (`fieldType`) USING BTREE,
  KEY `SchemaData_linkType_IDX` (`linkType`) USING BTREE,
  KEY `SchemaData_schemaId_IDX` (`schemaId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2283509 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SchemaField` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `schemaId` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SchemaField_schemaId_IDX` (`schemaId`,`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=912 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SchemaLinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linkType` varchar(100) NOT NULL DEFAULT '',
  `linkId` int(11) DEFAULT NULL,
  `schemaId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11165 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SchmGroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(100) DEFAULT NULL,
  `countOfParams` int(11) DEFAULT NULL,
  `assignedId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SchmGroup_id_IDX` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3774 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SchmTypeInt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldDataId` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SchmTypeString_fieldDataId_IDX` (`fieldDataId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SchmTypeString` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldDataId` int(11) DEFAULT NULL,
  `value` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SchmTypeString_fieldDataId_IDX` (`fieldDataId`) USING HASH,
  KEY `SchmTypeString_value_IDX` (`value`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2263913 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `SchmTypeText` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldDataId` int(11) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15489 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL;

        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = <<<SQL
DROP TABLE `Schema`;
DROP TABLE `SchemaData`;
DROP TABLE `SchemaField`;
DROP TABLE `SchemaLinks`;
DROP TABLE `SchmGroup`;
DROP TABLE `SchmTypeInt`;
DROP TABLE `SchmTypeString`;
DROP TABLE `SchmTypeText`;
SQL;

        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Создание таблиц схемы и связанных данных";
    }

    public function isConfirmed($returnBoolean = false)
    {
        $tables = ['Schema', 'SchemaData', 'SchemaField', 'SchemaLinks', 'SchmGroup', 'SchmTypeInt', 'SchmTypeString', 'SchmTypeText'];
        foreach ($tables as $table) {
            if (!$this->tableExist($table)) {
                return false;
            }
        }
        return true;
    }

    /*
     * ALTER TABLE `catItems`
     * DROP COLUMN `top`;
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