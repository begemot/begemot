<?php

/**
 * This is the model class for table "SchemaLinks".
 *
 * The followings are the available columns in table 'SchemaLinks':
 * @property integer $id
 * @property string $linkType
 * @property integer $linkId
 * @property integer $schemaId
 */
class SchemaLinks extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'SchemaLinks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('linkId, schemaId', 'numerical', 'integerOnly'=>true),
			array('linkType', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, linkType, linkId, schemaId', 'safe', 'on'=>'search'),
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
			'linkType' => 'Link Type',
			'linkId' => 'Link',
			'schemaId' => 'Schema',
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
		$criteria->compare('linkType',$this->linkType,true);
		$criteria->compare('linkId',$this->linkId);
		$criteria->compare('schemaId',$this->schemaId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => [
                'pageSize' => 100,
            ],
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SchemaLinks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    protected function beforeDelete()
    {
		/**
		 * Нужно удалить все данные связанные с этой schemaLinks. 
		 * - это записи в SchemaData, по linkId->groupId
		 * 
		 * Данные SchemaData лежат дальше в таблице в соответсствии с типами данных и удаляются каскадно
		 * 
		 */

		$schemaDataForDelete = SchemaData::model()->findAllByAttributes(['groupId'=>$this->linkId]);

		foreach ($schemaDataForDelete as $schemaData){
			
			if (!$schemaData->delete()){
				throw new Exception('Не удалось удалить SchemaData c linkId '.$schemaData->id);
				
			}
		}

        return parent::beforeDelete();
    }
}
