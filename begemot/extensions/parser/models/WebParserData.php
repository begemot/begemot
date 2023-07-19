<?php

/**
 * This is the model class for table "webParserData".
 *
 * The followings are the available columns in table 'webParserData':
 * @property integer $id
 * @property integer $processId
 * @property string $fieldName
 * @property string $fieldId
 * @property string $fieldData
 * @property integer $parentDataId
 */
class WebParserData extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WebParserData the static model class
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
		return 'webParserData';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('processId, parentDataId', 'numerical', 'integerOnly'=>true),
			array('fieldName', 'length', 'max'=>45),
			array('fieldId', 'length', 'max'=>500),
			array('fieldData', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, processId, fieldName, fieldId, fieldData, parentDataId', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'processId' => 'Process',
			'fieldName' => 'Field Name',
			'fieldId' => 'Field',
			'fieldData' => 'Field Data',
			'parentDataId' => 'Parent Data',
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
		$criteria->compare('processId',$this->processId);
		$criteria->compare('fieldName',$this->fieldName,true);
		$criteria->compare('fieldId',$this->fieldId,true);
		$criteria->compare('fieldData',$this->fieldData,true);
		$criteria->compare('parentDataId',$this->parentDataId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}