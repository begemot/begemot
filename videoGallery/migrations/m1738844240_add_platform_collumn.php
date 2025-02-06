<?php

class m1738844240_add_platform_collumn extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true)) {
            return false;
        }

        $sql = "ALTER TABLE `video_gallery_video`
                ADD COLUMN `platform` VARCHAR(255) NULL;"; // Замените `some_column` на существующую колонку для правильного расположения

        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if (!$this->isConfirmed(true)) {
            return false;
        }

        $sql = "ALTER TABLE `video_gallery_video`
                DROP COLUMN `platform`;";

        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Добавление колонки 'platform' в таблицу 'video_gallery_video'";
    }

    public function isConfirmed($returnBoolean = false)
    {
        // Проверяем, существует ли колонка 'platform' в таблице 'video_gallery_video'
        return $this->columnExist('video_gallery_video', 'platform');
    }

    /*
     * Если необходимо использовать транзакцию:
     *
     public function safeUp()
     {
     }

     public function safeDown()
     {
     }
     */
}
