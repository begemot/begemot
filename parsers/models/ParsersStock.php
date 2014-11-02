<?php

/**
 * This is the model class for table "parsers_stock".
 *
 * The followings are the available columns in table 'parsers_stock':
 * @property string $id
 * @property integer $price
 * @property string $name
 * @property text $text
 * @property integer $quantity
 * @property string $filename
 * @property integer $linked
 */
class ParsersStock extends CActiveRecord
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
		return 'parsers_stock';
	}
        
        
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, filename', 'required'),
			array('price, quantity, linked', 'numerical', 'integerOnly'=>true),
			array('name, id, filename', 'length', 'max'=>100),
			array('text', 'length', 'max'=>1000),
			array('id', 'unique')
		);
	}

	public function findedByArticle()
	{	
		$catItem = CatItem::model()->find(array('condition' => "`article`='" . $this->id . "'"));

		if ($catItem) {
			return $catItem->id;
		}

		return false;
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