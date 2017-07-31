<?php

/**
 * This is the model class for table "catItemsToItems".
 *
 * The followings are the available columns in table 'catItemsToItems':
 * @property integer $toItemId
 * @property integer $itemId
 */
class CatColor extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatItemsToCat the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function isLinkedWithColor($catItemId){
		$color = CatColorToCatItem::model()->findByAttributes(['catItemId'=>$catItemId,'colorId'=>$this->id]);

		if (!is_null($color)){
			return true;
		} else {
			return false;
		}
	}
        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'catColors';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,', 'required'),
			array('id,colorCode', 'safe'),

		);
	}

}