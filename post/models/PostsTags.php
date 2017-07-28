<?php

/**
 * This is the model class for table "argo_posts_tags".
 *
 * The followings are the available columns in table 'argo_posts_tags':
 * @property integer $id
 * @property string $tag_name
 * @prorerty 
 */
class PostsTags extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PostsTags the static model class
     */
    private static $tags = null;
    private static $tagsT = null;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'argo_posts_tags';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
// NOTE: you should only define rules for those attributes that
// will receive user inputs.
        return array(
            array('tag_name,tag_name_t,title_seo, tag_text', 'safe'),
            array('tag_name', 'length', 'max' => 20),
            // The following rule is used by search().
// Please remove those attributes that should not be searched.
            array('id, tag_name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
// NOTE: you may need to adjust the relation name and the related
// class name for the relations automatically generated below.
        return array(
            'posts'=>array(self::HAS_MANY, 'Posts', 'tag_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'tag_name' => 'Tag Name',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
// Warning: Please modify the following code to remove attributes that
// should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('tag_name', $this->tag_name, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    public static function getTags() {

        return self::loadTags();
    }

    public static function loadTags() {

        if (self::$tags == null) {
            
            $models = PostsTags::model()->findAll();
            $items = array(0 => 'Черновик');
            $itemsT = array();
            foreach ($models as $model) {
                $items[$model->id] = $model->tag_name;
                $itemsT[$model->id] = $model->tag_name_t;
            }
            self::$tags = $items;
            self::$tagsT = $itemsT;
        } 
        return self::$tags;
    }
    
    public static function getTagName($id){

       $tags = self::loadTags();
       
       return $tags[$id];
    }
    
    public static function getTagNameT($id){
       self::loadTags();

       return self::$tagsT[$id];
    }
    
       public function beforeSave() {

            $this->tag_name_t = $this->mb_transliterate($this->tag_name);

            return true;
        
    }

    public function behaviors() {
        return array(
        'SlugBehavior' => array(
            'class' => 'begemot.extensions.SlugBehavior',
        ));
    }
    
    
}