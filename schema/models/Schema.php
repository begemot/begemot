<?php

/**
 * This is the model class for table "Schema".
 *
 * The followings are the available columns in table 'Schema':
 * @property integer $id
 * @property string $name
 * @property integer $pid
 */
class Schema extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'Schema';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pid', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 200),

            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, pid', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'pid' => 'Pid',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('pid', $this->pid);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Schema the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getSchemaByName($name)
    {

        if ($model = self::model()->findByAttributes(['name' => $name])) {

            return $model;
        } else {
            $model = new Schema();
            $model->name = $name;
            $model->save();
            return $model;
        }
    }

    public function getAll()
    {
        $allList = [];
        $sql = 'select DISTINCT groupCode FROM SchemaData;';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($result as $id => $line) {
            $allList[$id] = $line['groupCode'];
        }
        // print_r($result);
        return $allList;
    }



}
