<?php

/**
 * This is the model class for table "gallery".
 *
 * The followings are the available columns in table 'gallery':
 * @property integer $id
 * @property string $name
 * @property string $name_t
 * @property string $text
 * @property integer $order
 */
Yii::import('begemot.extensions.contentKit.ContentKitModel');
class Gallery extends ContentKitModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Gallery the static model class
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
		return 'gallery';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = array(
			array('text,seo_title', 'safe'),
			array('order', 'numerical', 'integerOnly'=>true),
			array('name, name_t', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, name_t, text, order', 'safe', 'on'=>'search'),
		);

        return array_merge($rules,parent::rules());

	}
        
        public function behaviors(){
            $behaviors = array(
                'slug'=>array(
                    'class' => 'begemot.extensions.SlugBehavior',
                    
                ),  

            );
            //return $behaviors;123
            return array_merge($behaviors,parent::behaviors());
        }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название галлереи',
			'name_t' => 'Name T',
			'text' => 'Описание',
			'order' => 'Order',
		);
	}

        public function beforeSave(){
            parent::beforeSave();
            $this->name_t = $this->mb_transliterate($this->name);
            $this->orderBeforeSave();
            return true;
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
		$criteria->compare('text',$this->text,true);
		$criteria->compare('order',$this->order);
                
                if (!isset($_REQUEST[__CLASS__.'_sort']))
                $criteria->order = '`order`';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}