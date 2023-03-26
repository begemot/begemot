<?php

/**
 * This is the model class for table "SchemaData".
 *
 * The followings are the available columns in table 'SchemaData':
 * @property integer $id
 * @property string $value
 * @property integer $fieldId
 * @property integer $groupId
 */
class SchemaData extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'SchemaData';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('fieldId,groupId,schemaId', 'numerical', 'integerOnly' => true),


            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,  fieldId, catId', 'safe', 'on' => 'search'),
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

            'fieldId' => 'Field',
            'catId' => 'Cat',
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

        $criteria->compare('fieldId', $this->fieldId);
        $criteria->compare('catId', $this->catId);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SchemaData the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function setData($value, $type)
    {

        /*
         * Тонкий момент. Если тип указать отличный от текущего, то
         * по идее нужно брать модель значений из двух разных таблиц
         * и сводить. И старую удалить еще.
         */

        Yii::import('schema.models.types.*');


        //Проверяем соответствие типов
        if ($this->fieldType != $type) {
            $className = 'SchmType' . $this->fieldType;

            $oldValueSchema = $className::model()->findByAttributes(
                ['fieldDataId' => $this->id]
            );

        }

        $className = 'SchmType' . $type;

        $model = $className::model()->findByAttributes(
            ['fieldDataId' => $this->id]
        );


        if (!$model) {
            $model = new $className();
            $model->fieldDataId = $this->id;
        }

        $this->valueId = $model->id;
        $this->fieldType = $type;
        $model->value = $value;

        if ($model->save()) {
            $this->valueId = $model->id;

            if (isset($oldValueSchema) && $oldValueSchema) {
                $oldValueSchema->delete();
            }
            $this->save();
        } else {
            $errors = var_dump($model->errors);
            throw new Exception('Не удалось создать ' . $className . ' ' . $errors);
        }

    }

    public function getValue()
    {
        switch ($this->fieldType) {
            case 'String':
                return SchmTypeString::model()->findByPk($this->valueId)->value;
                break;
            case 'Int':
                return SchmTypeInt::model()->findByPk($this->valueId)->value;
                break;
            case 'Text':
                return SchmTypeText::model()->findByPk($this->valueId)->value;
                break;
        }

    }

    public function delete()
    {
        Yii::import('schema.models.types.*');
        if ($this->fieldType == 'String') {
            $data = SchmTypeString::model()->findByAttributes([
                'fieldDataId' => $this->id
            ]);

        } else if ($this->fieldType == 'Text') {
            $data = SchmTypeText::model()->findByAttributes([
                'fieldDataId' => $this->id
            ]);
        } else {
            throw new Exception('Сделать для других типов данных. Неизвестный тип данных:' . $this->fieldType);
        }
        if ($data)
            $data->delete();

        parent::delete();
    }
}
