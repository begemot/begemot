<?php

class VideoEntityLink extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'video_entity_link';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('video_id, entity_model, entity_id', 'required'),
            array('video_id, entity_id', 'numerical', 'integerOnly' => true),
            array('entity_model', 'length', 'max' => 255),
            array('id, video_id, entity_model, entity_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'video' => array(self::BELONGS_TO, 'VideoGalleryVideo', 'video_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'video_id' => 'ID видео',
            'entity_model' => 'Модель сущности',
            'entity_id' => 'ID сущности',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('video_id', $this->video_id);
        $criteria->compare('entity_model', $this->entity_model, true);
        $criteria->compare('entity_id', $this->entity_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return VideoEntityLink the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
