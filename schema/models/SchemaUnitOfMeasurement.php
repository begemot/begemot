<?php

/**
 * This is the model class for table "SchemaUnitOfMeasurement".
 *
 * The followings are the available columns in table 'SchemaUnitOfMeasurement':
 * @property integer $id
 * @property string $name
 * @property string $abbreviation
 * @property string $description
 */
class SchemaUnitOfMeasurement extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return SchemaUnitOfMeasurement the static model class
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
        return 'SchemaUnitOfMeasurement';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max'=>100),
            array('abbreviation', 'length', 'max'=>20),
            array('description', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, abbreviation, description', 'safe', 'on'=>'search'),
        );
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
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'abbreviation' => 'Abbreviation',
            'description' => 'Description',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    // public function search()
    // {
    //     // Warning: Please modify the following code to remove attributes that
    //     // should not be searched.

    //     $criteria=new CDbCriteria;

    //     $criteria->compare('id',$this->id);
    //     $criteria->compare('name',$this->name,true);
    //     $criteria->compare('abbreviation',$this->abbreviation,true);
    //     $criteria->compare('description',$this->description,true);

    //     return new CActiveDataProvider($this, array(
    //         'criteria'=>$criteria,
    //     ));
    // }

    public static function getAllUMList()
    {
        $models = self::model()->findAll();
        $list = array();

        foreach ($models as $model) {
            $list[$model->id] = $model->attributes;
        }

        return $list;
    }
}