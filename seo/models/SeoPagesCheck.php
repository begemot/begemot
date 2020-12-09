<?php

class SeoPagesCheck extends CActiveRecord
{



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seoPagesCheck';
	}

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(

            array('id', 'safe', 'on'=>'search'),
        );
    }

    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $this->id = $id = (isset($_REQUEST['SeoPagesCheck']['id'])?$_REQUEST['SeoPagesCheck']['id']:null);

        $criteria->compare('id',$this->id);
//        $criteria->compare('uid',$this->uid,true);
//        $criteria->compare('url',$this->url,true);
//        $criteria->compare('text_unique',$this->text_unique,);


        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeoLinks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
