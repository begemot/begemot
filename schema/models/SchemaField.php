<?php

/**
 * This is the model class for table "SchemaField".
 *
 *
 *
 * The followings are the available columns in table 'SchemaField':
 * @property integer $id
 * @property string $name
 * @property integer $scemaId
 * @property string $type
 */

Yii::import('schema.models.types.*');
class SchemaField extends CActiveRecord
{

    public static function getSchemaFieldByName($name, $schemaId=null)
    {
        if ($schemaId!=null){

            $model = self::model()->findByAttributes(['name' => $name, 'schemaId' => $schemaId]);
        } else {
            $model = self::model()->findAllByAttributes(['name' => $name]);

            if (is_array($model) && count($model)>1){
                throw new Exception('Полей с таким именем больше одного у разных Schema. Нужно указать id схемы при вызове');
            } else{
                if (isset($model[0])){
                    $model=$model[0];
                } else if (count($model)==0){
                    $model = false;
                }
            }
        }

        if ($model) {
            return $model;
        } else {
            $model = new SchemaField();
            $model->name = $name;
            $model->schemaId = $schemaId;
            $model->type = 'String';
            if ($model->save()) {
                return $model;
            } else {
                return false;
            }
        }

    }


    public function setFieldData($value,$fieldId,$linkedDataId,$linkType){
//        $linkType = ;
        self::setData($fieldId,$value,$this->schemaId,$linkedDataId,$linkType);
    }

    public static function setData($fielddName,$value,$schemaId, $groupId = null,$linkType)
    {

        $field = self::getSchemaFieldByName($fielddName,$schemaId);

        $model = SchemaData::model()->findByAttributes([
            'fieldId'=>$field->id,
            'schemaId'=>$schemaId,
            'groupId'=>$groupId,
            'linkType'=>$linkType
        ]);

        if (!$model) {
            $model = new SchemaData();
            $model->fieldId = $field->id;
            $model->schemaId = $schemaId;
            $model->groupId = $groupId;
            $model->fieldType = $field->type;
            $model->linkType = $linkType;
        }

      if ($model->save()){
          $model->setData ($value,$field->type);
      }
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'SchemaField';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('schemaId', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 100),
            array('type', 'length', 'max' => 20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, scemaId, type', 'safe', 'on' => 'search'),
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
            'scemaId' => 'Scema',
            'type' => 'Type',
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
        $criteria->compare('scemaId', $this->scemaId);
        $criteria->compare('type', $this->type, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SchemaField the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }




}
