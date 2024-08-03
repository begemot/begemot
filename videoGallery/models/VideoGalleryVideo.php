<?php

class VideoGalleryVideo extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'video_gallery_video';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('title_t, text, pub_date, create_time, update_time, published, authorId', 'required'),
            array('pub_date, create_time, update_time, published, authorId, top', 'numerical', 'integerOnly' => true),
            array('title_t', 'length', 'max' => 255),
            array('title, url', 'length', 'max' => 255),
            array('id, title_t, title, text, url, pub_date, create_time, update_time, published, authorId, top', 'safe', 'on' => 'search'),
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
            'title_t' => 'Название (Title T)',
            'title' => 'Название',
            'text' => 'Текст',
            'url' => 'URL',
            
            'pub_date' => 'Дата публикации',
            'create_time' => 'Время создания',
            'update_time' => 'Время обновления',
            'published' => 'Опубликовано',
            // 'order' => 'Порядок',
            'authorId' => 'ID автора',
            'top' => 'Верхний',
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

        $criteria->compare('title', $this->title, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return VideoGalleryVideo the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
