<?php

/**
 * This is the model class for table "promoRelation".
 *
 * The followings are the available columns in table 'promoRelation':
 * @property integer $id
 * @property integer $promoId
 * @property integer $type
 * @property integer $targetId
 */
class PromoRelation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PromoRelation the static model class
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
		return 'promoRelation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('promoId, type, targetId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, promoId, type, targetId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
    public function relations()
    {
        return array(
            'promo' => array(self::BELONGS_TO, 'Promo', 'promoId'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'promoId' => 'Promo',
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
		$criteria->compare('promoId',$this->promoId);
		$criteria->compare('type',$this->type);
		$criteria->compare('targetId',$this->targetId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}