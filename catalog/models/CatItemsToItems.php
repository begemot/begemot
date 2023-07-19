<?php

/**
 * This is the model class for table "catItemsToItems".
 *
 * The followings are the available columns in table 'catItemsToItems':
 * @property integer $toItemId
 * @property integer $itemId
 */
class CatItemsToItems extends CActiveRecord
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
        
        // public function behaviors(){
        //         return array(
        //                 'CBOrderModelBehavior' => array(
        //                         'class' => 'begemot.extensions.order.BBehavior.CBOrderModelBehavior',
                               
        //                 )
        //         );
        // }   
        
        public function relations()
        {
            return array(
                'item'=>array(self::BELONGS_TO, 'CatItem', 'itemId'),
                'toItem'=>array(self::BELONGS_TO, 'CatItem', 'toItemId'),
            );
        }
        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'catItemsToItems';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('toItemId, itemId', 'required'),
			array('toItemId, itemId, order', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('toItemId, itemId', 'safe', 'on'=>'search'),
		);
	}

}