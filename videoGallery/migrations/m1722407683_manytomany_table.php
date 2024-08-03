<?php

class m1722407683_manytomany_table extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true) == true) return false;

        $sql = "CREATE TABLE video_entity_link (
            id INT(10) NOT NULL AUTO_INCREMENT,
            video_id INT(10) NOT NULL,
            entity_model VARCHAR(255) NOT NULL,
            entity_id INT(10) NOT NULL,
            PRIMARY KEY (id),
            KEY FK_video_entity_link_video (video_id),
            CONSTRAINT FK_video_entity_link_video FOREIGN KEY (video_id) REFERENCES video_gallery_video (id)
        )
        ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb3
        COLLATE=utf8mb3_general_ci;
        ";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "DROP TABLE video_entity_link;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Создание таблицы video_entity_link для связывания видео с другими сущностями.";
    }

    public function isConfirmed($returnBoolean = false)
    {
        return $this->tableExist('video_entity_link');
    }

    /*
     * ALTER TABLE `catItems`
     * DROP COLUMN `top`;
     *
     * Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
