<?php

class m20191014_031141_seoPagesCheck extends Migrations
{
    public function up()
    {

        if ($this->isConfirmed(true) == true) return false;

        $sql = "
        CREATE TABLE IF NOT EXISTS `seo_textru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userkey` varchar(45) DEFAULT NULL COMMENT 'ключ ip',
  `uid` varchar(45) DEFAULT NULL COMMENT 'id проверки',
  `text_unique` int(11) DEFAULT NULL COMMENT 'процент уникальности текста',
  `clear_text` longtext COMMENT 'Текст очищенный от тегов и мусора сайтом text.ru',
  `count_chars_with_space` int(11) DEFAULT NULL COMMENT 'Количество симоволо с пробелами',
  `count_chars_without_space` int(11) DEFAULT NULL COMMENT 'Количество символов без пробелов',
  `count_words` int(11) DEFAULT NULL COMMENT 'Количество слов',
  `water_percent` int(11) DEFAULT NULL COMMENT 'Процент воды',
  `spam_percent` int(11) DEFAULT NULL COMMENT 'Процент спама',
  `mixed_words` int(11) DEFAULT NULL COMMENT 'Слова с похожим написанием, но с использованием в одном слове символов из разных алфавитов. ',
  `request_date` int(11) DEFAULT NULL COMMENT 'Дата и время когда отправлялся запрос на проверку',
  `error_code` int(11) DEFAULT NULL COMMENT 'код ошибки от text.ru',
  `error_desc` mediumtext COMMENT 'Описание ошибки',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;
        
        
        CREATE view 
 `seoPagesCheck` AS 
select `t1`.`id` AS `id`,`t1`.`url` AS `url`,`t1`.`uid` AS `uid`,`t2`.`text_unique` AS `text_unique`,`t1`.`checkError` AS `checkError` from (`seo_pages` `t1` left join `seo_textru` `t2` on((`t1`.`uid` = `t2`.`uid`)));
";
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        if ($this->isConfirmed(true) == false) return false;

        $sql = "
        DROP VIEW `seoPagesCheck`;
        DROP table `seo_textru`;
        ";
        $this->execute($sql);

        return true;
    }

    public function getDescription()
    {
        return "Таблица проверки текст.ру";
    }

    public function isConfirmed($returnBoolean = false)
    {

        $result = $this->tableExist('seoPagesCheck');

        if ($returnBoolean) {
            return $result;
        }

        return parent::confirmByWords($result);
    }

}