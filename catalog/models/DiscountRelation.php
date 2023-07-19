<?php

/**
 * This is the model class for table "discountRelation".
 *
 * The followings are the available columns in table 'discountRelation':
 * @property integer $id
 * @property integer $discountId
 * @property integer $type
 * @property integer $targetId
 */
class DiscountRelation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DiscountRelation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'discountRelation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('discountId, type, targetId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, discountId, type, targetId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
    public function relations()
    {
        return array(
            'discount' => array(self::BELONGS_TO, 'Discount', 'discountId'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'discountId' => 'Discount',
			'type' => 'Type',
			'targetId' => 'Target',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('discountId',$this->discountId);
		$criteria->compare('type',$this->type);
		$criteria->compare('targetId',$this->targetId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}