<?php

/**
 * This is the model class for table "webParser".
 *
 * The followings are the available columns in table 'webParser':
 * @property integer $id
 * @property string $date
 * @property string $report_text
 * @property string $processTime
 * @property integer $pagesProcessed
 * @property string $status
 * @property string $name
 */
class WebParser extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'webParser';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pagesProcessed', 'numerical', 'integerOnly'=>true),
			array('status, name', 'length', 'max'=>45),
			array('date, report_text, processTime', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, report_text, processTime, pagesProcessed, status, name', 'safe', 'on'=>'search'),
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
			'date' => 'Date',
			'report_text' => 'Report Text',
			'processTime' => 'Process Time',
			'pagesProcessed' => 'Pages Processed',
			'status' => 'Status',
			'name' => 'Name',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('report_text',$this->report_text,true);
		$criteria->compare('processTime',$this->processTime,true);
		$criteria->compare('pagesProcessed',$this->pagesProcessed);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WebParser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
