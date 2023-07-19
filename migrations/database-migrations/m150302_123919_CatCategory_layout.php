<?php

class m150302_123919_CatCategory_layout extends Migrations
{
	public function up()
	{
        if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catCategory`
        ADD `layout` varchar(255) COLLATE 'utf8_general_ci' NOT NULL,
        ADD `viewFile` varchar(255) COLLATE 'utf8_general_ci' NOT NULL,
        ADD `itemViewFile` varchar(255) COLLATE 'utf8_general_ci' NOT NULL;";
        $this->execute($sql);

        return true;
	}

	public function down()
	{
        if($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catCategory` DROP `layout`, DROP `viewFile`, DROP `itemViewFile`";

        return $this->execute($sql);
	}

    public function getDescription()
    {
        return "Добавление layout, viewFile, itemViewFile для раздела";
    }

    public function isConfirmed($returnBoolean = false){
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catCategory');
        $result = isset($table->columns['layout']) && isset($table->columns['viewFile'])  && isset($table->columns['itemViewFile']);

        if($returnBoolean){
            return $result;
        }

        return parent::confirmByWords($result);
    }
}