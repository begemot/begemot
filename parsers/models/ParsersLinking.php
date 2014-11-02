<?php

/**
 * This is the model class for table "parsers_stock".
 *
 * The followings are the available columns in table 'parsers_stock':
 * @property integer $id
 * @property integer $fromId
 * @property integer $toId
 */
class ParsersLinking extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatCategory the static model class
	 */
         
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**D ` 
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'parsers_linking';
	}
        
        
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fromId, toId, filename', 'required'),
			array('fromId, toId', 'unique')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'item'=>array(self::BELONGS_TO, 'CatItem', 'toId'),
			'linking'=>array(self::BELONGS_TO, 'ParsersStock', 'fromId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
		);
	}

}