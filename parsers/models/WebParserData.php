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
 * @property string $fieldModifId
 * @property string $sourcePageUrl
 * @property string $fieldParentId
 * @property string $fieldGroupId
 */
class WebParserData extends CActiveRecord
{
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
			array('fieldId, fieldModifId, fieldParentId, fieldGroupId', 'length', 'max'=>500),
			array('fieldData, sourcePageUrl', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, processId, fieldName, fieldId, fieldData, parentDataId, fieldModifId, sourcePageUrl, fieldParentId, fieldGroupId', 'safe', 'on'=>'search'),
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
			'fieldModifId' => 'Field Modif',
			'sourcePageUrl' => 'Source Page Url',
			'fieldParentId' => 'Field Parent',
			'fieldGroupId' => 'Field Group',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('processId',$this->processId);
		$criteria->compare('fieldName',$this->fieldName,true);
		$criteria->compare('fieldId',$this->fieldId,true);
		$criteria->compare('fieldData',$this->fieldData,true);
		$criteria->compare('parentDataId',$this->parentDataId);
		$criteria->compare('fieldModifId',$this->fieldModifId,true);
		$criteria->compare('sourcePageUrl',$this->sourcePageUrl,true);
		$criteria->compare('fieldParentId',$this->fieldParentId,true);
		$criteria->compare('fieldGroupId',$this->fieldGroupId,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 1000,
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WebParserData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
