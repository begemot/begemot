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
class ParsersStock extends CActiveRecord
{

	public $ids = array();
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
		return 'parsers_stock';
	}
        
   

	public function searchOld($id=null, $name = null, $ids = null, $file = null, $pagination = null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

        $criteria=new CDbCriteria;
                if ($id===null)

                    $criteria->compare('id',$this->id);
                else
                    $criteria->compare('id',$id);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('text',$this->text);
		$criteria->compare('quantity',$this->quantity,true);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('linked',$this->linked,true);

		//if ($name == 'newWithId') {
			$criteria->addInCondition("id", $ids);
			$criteria->addCondition('filename=' . $file . ' & linked=123');
            $criteria->order = 'id ASC';
		//}


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
		        'pageSize'=>20,
		    ),
		));
	}

	public function search($id=null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

        $criteria=new CDbCriteria;
                if ($id===null)

                    $criteria->compare('id',$this->id);
                else
                    $criteria->compare('id',$id);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('text',$this->text);
		$criteria->compare('quantity',$this->quantity,true);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('linked',$this->linked,true);
		if ($this->ids) {
			$criteria->addInCondition('id',$this->ids, 'AND');
		}
		


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
			array('id, filename', 'required'),
			array('price, quantity, linked', 'numerical', 'integerOnly'=>true),
			array('id, filename', 'length', 'max'=>300),
			array('text, images, parents, groups,modifs, name', 'length', 'max'=>100000),
			array('id', 'unique')
		);
	}

	public function findedByArticle()
	{	
		$catItem = CatItem::model()->find(array('condition' => "`article`='" . $this->id . "'"));

		if ($catItem) {
			return $catItem->id;
		}

		return false;
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
		);
	}


}