<?php

/**
 *
 * Модель данных для Акций в каталоге.
 *
 * @property integer $id
 * @property string $title
 * @property string $sale
 * @property string $maxSale
 * @property string $minSale
 * @property integer $active
 */

Yii::import('begemot.extensions.contentKit.ContentKitModel');

class Discount extends ContentKitModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Discount the static model class
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
		return 'discount';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        $rules = array(
			array('title', 'length', 'max'=>100),
			array('sale , maxSale, minSale, active', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, sale', 'safe', 'on'=>'search'),
		);

        return array_merge(parent::rules(), $rules);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array_merge( array(
			'id' => 'ID',
			'title' => 'Title',
			'sale' => 'Скидка(в цифрах)',
			'minSale' => 'Минимальная скидка(в цифрах)',
			'maxSale' => 'Максимальная скидка(в цифрах)',
			'active' => 'Активна'
		),
		parent::attributeLabels());
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('sale',$this->sale,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}




}