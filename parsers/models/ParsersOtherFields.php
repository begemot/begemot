<?php

/**
 * This is the model class for table "parsers_stock".
 *
 * The followings are the available columns in table 'parsers_stock':
 * @property string $id
 * @property integer $price
 * @property string $name
 * @property text $text
 * @property integer $quantity
 * @property string $filename
 * @property integer $linked
 */
class ParsersOtherFields extends CActiveRecord
{

	public $ids = array();

         
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**D ` 
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'parsers_other_fields';
	}
        
   


//
//	public function search($id=null)
//	{
//		// Warning: Please modify the following code to remove attributes that
//		// should not be searched.
//
//        $criteria=new CDbCriteria;
//                if ($id===null)
//
//                    $criteria->compare('id',$this->id);
//                else
//                    $criteria->compare('id',$id);
//
//		$criteria->compare('name',$this->name,true);
//		$criteria->compare('parsers_stock_id',$this->text);
//		$criteria->compare('filename',$this->filename);
//
//		return new CActiveDataProvider($this, array(
//			'criteria'=>$criteria,
//
//		));
//	}
        
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,parsers_stock_id,value,filename', 'required'),

		);
	}





}