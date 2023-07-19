<?php

/**
 * This is the model class for table "ParsersImagesIgrone".
 */
class ParsersImagesIgrone extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatCategory the static model class
	 */
         
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**D ` 
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'parsers_images_ignore';
	}

	public function search($hash=null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

        $criteria=new CDbCriteria;
                if ($hash===null)

                    $criteria->compare('hash',$this->hash);
		


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,

		));
	}
        
        
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hash', 'required'),
		);
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'hash' => "Хэш изображения"
		);
	}

}