<?php

/**
 * This is the model class for table "slider".
 *
 * The followings are the available columns in table 'slider':
 * @property integer $id
 * @property string $image
 * @property string $header
 * @property string $text1
 * @property string $text2
 * @property string $text3
 * @property integer $order
 */
class Slider extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'slider';
	}

  public function behaviors(){
          return array(
                  'CBOrderModelBehavior' => array(
                          'class' => 'begemot.extensions.order.BBehavior.CBOrderModelBehavior',
                  )
          );
   }
   
   public function defaultScope()
   {
       return array(
       'order'=>'`order` ASC'
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
			array('id, order', 'numerical', 'integerOnly'=>true),
         array('header, text1, text2, text3', 'type', 'type'=>'string'),
			array('id, image, header, text1, text2, text3, order', 'safe', 'on'=>'search'),
         array('image', 'file', 'on'=>'create', 'allowEmpty' => false, 'types'=>'jpg, gif, png'),
         // array('image', 'file', 'on'=>'update', 'allowEmpty' => true, 'types'=>'jpg, gif, png'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
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
			'image' => 'Изображение',
			'header' => 'Заголовок',
			'text1' => 'Первая строка',
			'text2' => 'Вторая строка',
			'text3' => 'Третья строка',
			'order' => 'Order',
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
		$criteria->compare('image',$this->image,true);
		$criteria->compare('header',$this->header,true);
		$criteria->compare('text1',$this->text1,true);
		$criteria->compare('text2',$this->text2,true);
		$criteria->compare('text3',$this->text3,true);
		$criteria->compare('order',$this->order);
      if (!isset($_REQUEST[__CLASS__.'_sort']))
         $criteria->order = '`order`';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
         'pagination'=>array(
            'pageSize'=>10,
         ),
		));
	}

   protected function beforeSave()
   {
      if(parent::beforeSave()){
         $this->orderBeforeSave();
         return True;
      } else {
         return False;
      }
   }
   
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Slider the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
