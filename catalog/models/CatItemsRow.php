<?php

/**
 * This is the model class for table "catItemsRow".
 *
 * The followings are the available columns in table 'catItemsRow':
 * @property integer $id
 * @property integer $catItemId
 * @property string $name
 * @property string $name_t
 * @property integer $type
 * @property integer $data
 */
class CatItemsRow extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatItemsRow the static model class
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
		return 'catItemsRow';
	}
        
        public function behaviors(){
            return array(
                'slug'=>array(
                    'class' => 'begemot.extensions.SlugBehavior',
                ),
            );
        }
        
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type', 'required'),
			array('name, name_t', 'length', 'max'=>100),
			array('id, name, name_t, type, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, name_t, type, data', 'safe', 'on'=>'search'),
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
        

        public function beforeSave(){
            $nameTemp = explode("|", $this->name);
            $nameTemp = $nameTemp[0];
            $this->name_t = $this->mb_transliterate($this->name);
            return true;
        }
        
        public function afterSave(){
            Yii::app()->db->createCommand()->addColumn('catItems',$this->name_t,'text');;
            return true;
        }
        public function beforeDelete(){
            Yii::app()->db->createCommand()->dropColumn('catItems',$this->name_t);;
            return true;
        }   

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',

            'name' => 'Значение поля',
            'name_t' => 'Транслит названия',
			'type' => 'Тип',
			'data' => 'Информация',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_t',$this->name_t,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('data',$this->data);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
