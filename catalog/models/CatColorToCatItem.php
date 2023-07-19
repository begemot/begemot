<?php

class CatColorToCatItem extends CActiveRecord
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function relations()
	{
		return array(
			'color'=>array(self::BELONGS_TO, 'CatColor', 'colorId'),

		);
	}

        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'catColorsToCatItem';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('catItemId,colorId', 'required'),
			array('id', 'safe'),

		);
	}

}