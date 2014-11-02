<?php

/**
 * This is the model class for table "CatItemsToCat".
 *
 * The followings are the available columns in table 'CatItemsToCat':
 * @property integer $catId
 * @property integer $itemId
 * @property integer $order
 */
class CatItemsToCat extends CActiveRecord
{

   public $item_name;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatItemsToCat the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function behaviors(){
                return array(
                        'CBOrderModelBehavior' => array(
                                'class' => 'begemot.extensions.order.BBehavior.CBOrderModelBehavior',
                        )
                );
        }   
        
        public function relations()
        {
            return array(
                'item'=>array(self::BELONGS_TO, 'CatItem', 'itemId'),
                'cat'=>array(self::BELONGS_TO, 'CatCategory', 'catId'),
            );
        }
        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'catItemsToCat';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('catId, itemId', 'required'),
			array('catId, itemId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('catId, itemId, item_name', 'safe', 'on'=>'search'),
		);
	}
        
	public function search($id=null)
	{
		$criteria=new CDbCriteria;
      $criteria->with = 'item';
      $criteria->condition = '`t`.`catId`='.$id.'';
      $criteria->compare('itemId',$this->itemId,true);
      $criteria->compare('catId',$this->catId,true);
      $criteria->compare('item.name',$this->item_name, true);
      $criteria->order = 't.order';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public function beforeSave(){
            if ($this->isNewRecord){
                $result = count( $this->model()->findAll(array('condition'=>'catId ='.$this->catId.' and itemId='.$this->itemId)));

                if ($result!=0) 
                    return false;
                else{
                    $this->order = $this->getLastOrderValue();
                    return true;
                }
            } return true;
        }

}