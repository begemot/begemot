<?php

/**
 * This is the model class for table "ParsersImagesIgrone".
 */
class ParsersIgnoreImages extends CActiveRecord
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
		return 'parsers_ignore_images';
	}

    public function search($id=null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

        $criteria=new CDbCriteria;
        if ($id===null)

            $criteria->compare('id',$this->id);
        else
            $criteria->compare('id',$md5);

        $criteria->compare('sha1',$this->sha1,true);
        $criteria->compare('md5',$this->md5,true);
		$criteria->compare('image',$this->image,true);
		


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
			array('md5, sha1, image', 'required'),
			array('sha1, md5', 'unique')
		);
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'md5' => "Хэш изображения md5",
			'sha1' => "Хэш изображения sha1",
			'image' => "Image"
		);
	}

}