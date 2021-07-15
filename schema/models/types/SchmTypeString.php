<?php
/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 12.02.2021
 * Time: 12:30
 */

class SchmTypeString extends CActiveRecord
{
    public function tableName()
    {
        return 'SchmTypeString';
    }

    public function relations()
    {

        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(

        );
    }
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('fieldDataId', 'numerical','integerOnly' => true),
            array('value', 'length', 'max' => 200),

        );
    }
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        //$criteria->compare('id', $this->id);
       // $criteria->compare('name', $this->name, true);
       // $criteria->compare('pid', $this->pid);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}