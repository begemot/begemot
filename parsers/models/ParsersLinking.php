<?php

/**
 * This is the model class for table "parsers_stock".
 *
 * The followings are the available columns in table 'parsers_stock':
 * @property integer $id
 * @property integer $fromId
 * @property integer $toId
 */
class ParsersLinking extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CatCategory the static model class
     */

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**D `
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'parsers_linking';
    }

    public function search($id = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        if ($id === null)
            $criteria->compare('id', $this->id);
        else
            $criteria->compare('id', $id);

        $criteria->compare('fromId', $this->fromId, true);
        $criteria->compare('toId', $this->toId, true);
        $criteria->compare('filename', $this->filename, true);


        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,

        ));
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $rules =  array(
            array('fromId, toId, filename', 'required'),
            array('fromId, toId', 'unique'),
            array('id, fromId, toId, filename',  'safe', 'on' => 'search'),
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
            'item' => array(self::BELONGS_TO, 'CatItem', 'toId'),
            'linking' => array(self::BELONGS_TO, 'ParsersStock', 'fromId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
    }

}