<?php

class m1739515364_remove_foreing_key extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `video_gallery_video` DROP FOREIGN KEY `FK_video_gallery_video_video_gallery`;";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `video_gallery_video` ADD CONSTRAINT `FK_video_gallery_video_video_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `video_gallery` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Удаление внешнего ключа FK_video_gallery_video_video_gallery из video_gallery_video";
    }

    public function isConfirmed($returnBoolean = false)
    {

        return !$this->foreignKeyExists('video_gallery_video', 'FK_video_gallery_video_video_gallery');
    }
}