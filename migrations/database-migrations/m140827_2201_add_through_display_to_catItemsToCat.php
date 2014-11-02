<?php

class m140827_2201_add_through_display_to_catItemsToCat extends Migrations
{
	public function up()
	{
		if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catItemsToCat`
				ADD COLUMN `through_display` TINYINT DEFAULT 0;";
        return $this->execute($sql);
	}

	public function down()
	{
		if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catItemsToCat`
				DROP COLUMN `through_display`;";
        return $this->execute($sql);
	}

	public function getDescription()
	{
		return "Добавление поля 'through_display' в таблицу catItemsToCat для 'сквозного отображения'";
	}

	public function isConfirmed($returnBoolean = false){
		Yii::app()->db->schema->refresh();
		$table = Yii::app()->db->schema->getTable('catItemsToCat');
		$result = isset($table->columns['through_display']) ? true : false;

        if($returnBoolean) return $result;

        return parent::confirmByWords($result);
	}
}