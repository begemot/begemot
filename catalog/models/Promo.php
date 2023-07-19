<?php

/**
 *
 * Модель данных для Акций в каталоге.
 *
 * @property integer $id
 * @property string $title
 * @property string $order
 * @property string $text
 */

Yii::import('begemot.extensions.contentKit.ContentKitModel');

class Promo extends ContentKitModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Promo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	 public function behaviors(){
            return array(
                    'CBOrderModelBehavior' => array(
                            'class' => 'begemot.extensions.order.BBehavior.CBOrderModelBehavior',
                    )
            );
    } 

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'promo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        $rules = array(

            array('title,text,title2,title3,dateTo,dateFrom,sale', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, text', 'safe', 'on'=>'search'),
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
			'title' => 'Заголовок',
			'text' => 'Текст акции',
            'dateFrom' => 'Дата начала акции',
            'dateTo' => 'Дата конца акции',
            'title2' => 'Второй заголовок',
            'title3' => 'Третий заголовок',
            'sale'=>'Скидка'
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
		$criteria->compare('text',$this->text,true);
		$criteria->order = 't.order';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


    public function beforeSave() {
        if ($this->dateTo!='') {
            $format = 'd.m.Y'; // отличается от используемого в функции date

            $dateArray = date_parse_from_format($format,$this->dateTo);
            $this->dateTo = $timestamp = mktime(0,0,0,$dateArray['month'],$dateArray['day'],$dateArray['year']);

        }

        if ($this->dateFrom!='') {
            $format = 'd.m.Y'; // отличается от используемого в функции date

            $dateArray = date_parse_from_format($format,$this->dateFrom);
            $this->dateFrom = $timestamp = mktime(0,0,0,$dateArray['month'],$dateArray['day'],$dateArray['year']);

        }
        return parent::beforeSave();
    }



}