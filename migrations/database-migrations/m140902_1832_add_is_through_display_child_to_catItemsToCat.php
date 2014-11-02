<?php

class m140902_1832_add_is_through_display_child_to_catItemsToCat extends Migrations
{
	public function up()
	{
		if($this->isConfirmed(true) == true) return false;

        $sql = "ALTER TABLE `catItemsToCat`
				ADD COLUMN `is_through_display_child` TINYINT DEFAULT 0;";
        return $this->execute($sql);
	}

	public function down()
	{
		if ($this->isConfirmed(true) == false) return false;

        $sql = "ALTER TABLE `catItemsToCat`
				DROP COLUMN `is_through_display_child`;";
        return $this->execute($sql);
	}

	public function getDescription()
	{
		return "Добавление поля 'is_through_display_child' в таблицу catItemsToCat для категории 'сквозного отображения'";
	}

	public function isConfirmed($returnBoolean = false){
		Yii::app()->db->schema->refresh();
		$table = Yii::app()->db->schema->getTable('catItemsToCat');
		$result = isset($table->columns['is_through_display_child']) ? true : false;

        if($returnBoolean) return $result;

        return parent::confirmByWords($result);
	}
}