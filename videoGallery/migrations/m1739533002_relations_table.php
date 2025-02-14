<?php

class m1739533002_relations_table extends BaseMigration
{
    public function up()
    {
        if ($this->isConfirmed(true)) return false;

        // Проверяем, существует ли внешний ключ перед удалением
        if ($this->foreignKeyExists('video_entity_link', 'FK_video_entity_link_video')) {
            $dropFkSql = "ALTER TABLE video_entity_link DROP FOREIGN KEY FK_video_entity_link_video;";
            $this->execute($dropFkSql);
        }

        // Обновляем video_gallery_video.id, чтобы добавить UNSIGNED
        $alterTableSql = "ALTER TABLE video_gallery_video MODIFY COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT;";
        $this->execute($alterTableSql);

        // Создаём таблицу video_entity_relation
        $sql = "CREATE TABLE video_entity_relation (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            video_id INT UNSIGNED NOT NULL,
            entity_type VARCHAR(100) NOT NULL,
            entity_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            CONSTRAINT fk_video FOREIGN KEY (video_id) REFERENCES video_gallery_video(id) ON DELETE CASCADE,
            INDEX idx_entity (entity_type, entity_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        
        $this->execute($sql);

        // Восстанавливаем внешний ключ (если требуется)
        if ($this->foreignKeyExists('video_entity_link', 'FK_video_entity_link_video')) {
            $restoreFkSql = "ALTER TABLE video_entity_link ADD CONSTRAINT FK_video_entity_link_video FOREIGN KEY (video_id) REFERENCES video_gallery_video(id) ON DELETE CASCADE;";
            $this->execute($restoreFkSql);
        }

        return true;
    }

    public function down()
    {
        if (!$this->isConfirmed(true)) return false;

        $sql = "DROP TABLE IF EXISTS video_entity_relation;";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Создает таблицу для связи видео с разными сущностями";
    }

    public function isConfirmed($returnBoolean = false)
    {
        return $this->tableExist('video_entity_relation');
    }
}
