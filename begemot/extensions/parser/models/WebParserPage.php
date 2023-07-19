<?php

/**
 * This is the model class for table "webParserPage".
 *
 * The followings are the available columns in table 'webParserPage':
 * @property integer $id
 * @property string $content
 * @property string $url
 * @property integer $content_hash
 * @property integer $url_hash
 */
class WebParserPage extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return webParserPage the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'webParserPage';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(

            array('id', 'numerical', 'integerOnly' => true),
            array('url', 'length', 'max' => 1000),
            array('content', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, content, url, content_hash, url_hash, status', 'safe', 'on' => 'search'),
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

    protected function beforeSave()
    {

        if (parent::beforeSave() === false) {
            return false;
        }
        if (!is_null($this->content)) {

           $this->content_hash = md5($this->content);

        }

        if (!is_null($this->url)) {
             $this->url_hash = md5($this->url);
        }



        return true;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'content' => 'Content',
            'url' => 'Url',
            'content_hash' => 'Content Hash',
            'url_hash' => 'Url Hash',
            'status' => 'Status',
        );
    }




}