<?php

/**
 * This is the model class for table "reviews".
 *
 * The followings are the available columns in table 'reviews':
 * @property integer $id
 * @property integer $pid
 * @property integer $type
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $pluses
 * @property string $cons
 * @property string $general
 * @property integer $status
 * @property string $created_at
 */
class Reviews extends CActiveRecord
{
   /*
    * Reviews statuses
    */
   const STATUS_NOT_APPROWED = 0;
   const STATUS_APPROWED = 1;
   
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reviews';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name,general', 'required'),
			array('pid, type, status', 'numerical', 'integerOnly'=>true),
         array('created_at', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
         array('status', 'default', 'value' => '0', 'setOnEmpty' => true, 'on' => 'insert'),
			array('id, pid, type, name, email, phone, pluses, cons, general, status, created_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
         'category' => array(self::BELONGS_TO,'CatItem','id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'type' => 'Тип',
			'name' => 'Имя',
			'email' => 'Email',
			'phone' => 'Телефон',
			'pluses' => 'Плюсы',
			'cons' => 'Минусы',
			'general' => 'Общее впечатление',
			'status' => 'Статус',
			'created_at' => 'Создано',
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
		$criteria->compare('pid',$this->pid);
		$criteria->compare('type',$this->type);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('pluses',$this->pluses,true);
		$criteria->compare('cons',$this->cons,true);
		$criteria->compare('general',$this->general,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_at',$this->created_at,true);
      if (!isset($_REQUEST[__CLASS__.'_sort']))
         $criteria->order = '`created_at`';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
         'pagination'=>array(
            'pageSize'=>10,
         ),
		));
	}

    /*
     * Sets review as approved
     * @return boolean
     */
    public function setApproved()
    {
        $this->status = self::STATUS_APPROWED;
        return $this->update();

    }
   
   
   /**
   * Get full text representation of review
   * @return string
   */   
   public function getReviewText() {
      return Yii::t('ReviewsModule.msg', 'Pluses') . ": {$this->pluses} <br /> " . Yii::t('ReviewsModule.msg', 'Cons') . ": {$this->cons} <br /> " . Yii::t('ReviewsModule.msg', 'General') . ": {$this->general}";
   }

   
	public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'Status' => array(
				'0' => 'Новый',
				'1' => 'Подтвержденный',
			),			
         'Type' => array(
				'0' => 'Плохой',
				'1' => 'Хороший',
			),
		);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}
   
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Reviews the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
