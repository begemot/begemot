<?php

/**
 * This is the model class for table "companyEmployee".
 *
 * The followings are the available columns in table 'companyEmployee':
 * @property integer $id
 * @property string $name
 * @property string $position
 * @property string $text
 */
class CompanyEmployee extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'companyEmployee';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, position', 'length', 'max'=>200),
			array('text', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, position, text', 'safe', 'on'=>'search'),
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
			'departs' => array(self::MANY_MANY, 'CompanyDepart', 'companyEmpToDep(empId, depId)'/*,'order'=>'options_options.order ASC'*/),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Имя',
			'position' => 'Должность',
			'text' => 'Информация',
		);
	}
	public function behaviors()
	{
		$behaviors = array(
			'slug' => array(
				'class' => 'begemot.extensions.SlugBehavior',
			),
		);

		return array_merge($behaviors, parent::behaviors());
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function beforeSave()
	{
		parent::beforeSave();

		$this->nameT = $this->mb_transliterate($this->name);


		if ($this->isNewRecord) {

			$criteria = new CDbCriteria;

			$criteria->select = 'MAX(`order`) as `order`';


			$order = $this->find($criteria);

			$this->order = $order->order+1;

		}

		return true;
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CompanyEmployee the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
