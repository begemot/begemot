<?php

/**
 * This is the model class for table "catItems".
 *
 * The followings are the available columns in table 'catItems':
 * @property integer $id
 * @property string $name
 * @property string $name_t
 * @property integer $status
 * @property string $data
 * @property integer $quantity
 * @property integer $delivery_date
 * @property string $article
 */
Yii::import('begemot.extensions.contentKit.ContentKitModel');

class CatItem extends ContentKitModel
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CatItem the static model class
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
        return 'catItems';
    }

    public function behaviors()
    {
        $behaviors = array(
            'slug' => array(
                'class' => 'begemot.extensions.SlugBehavior',
            ),

        );

        return array_merge($behaviors, parent::behaviors());
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $rules = array(
            array('name', 'required'),
            array('status, quantity', 'numerical', 'integerOnly' => true),
            array('name, name_t, article', 'length', 'max' => 100),
            array('seo_title', 'length', 'max' => 255),
            // The following rule is used by search().
            array('id, name, name_t, status, data, price, text, name, delivery_date, quantity', 'safe'),
            // Please remove those attributes that should not be searched.
            array('id, name, name_t, status, data', 'safe', 'on' => 'search'),
        );
        return array_merge(parent::rules(), $rules);
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'name' => array(self::BELONGS_TO, 'CatItemsToCat', 'itemId'),
            'category' => array(self::BELONGS_TO, 'CatCategory', 'catId'),
            'reviews' => array(self::HAS_MANY, 'Reviews', 'pid', 'condition'=>'status=1')
        );
    }

    public function getOption()
    {
        $ids = CatItemsToItems::model()->findAll(array("condition" => 'itemId=' . $this->id, 'order' => 'id ASC'));
        $arrayOfIds = array();
        foreach ($ids as $id) {
            array_push($arrayOfIds, $id->toItemId);
        }
        $arrayOfIds = array_filter($arrayOfIds);
        return CatItem::model()->findAllByPk($arrayOfIds);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'name_t' => 'Name T',
            'status' => 'Status',
            'data' => 'Data',
            'delivery_date' => 'Дата поставки:',
            'quantity' => 'Количество',
        );
    }

    public function itemTableName()
    {
        return 'catItems_' . $this->id;
    }

    public function beforeSave()
    {
        parent::beforeSave();

        $this->name_t = $this->mb_transliterate($this->name);
        //$this->Video = $_REQUEST['CatItem']['Video'];
        $this->delivery_date = strtotime($this->delivery_date);
        $itemAdditionalRows = CatItemsRow::model()->findAll();
        if (is_array($itemAdditionalRows)) {

            foreach ($itemAdditionalRows as $itemRow) {

                $paramName = $itemRow->name_t;
                if (isset($_REQUEST['CatItem'][$itemRow->name_t]))
                    $this->$paramName = $_REQUEST['CatItem'][$itemRow->name_t];

            }
        }
        return true;
    }

    protected function afterFind()
    {
        $this->delivery_date = date('m/d/Y', $this->delivery_date);

        return parent::afterFind();
    }


    protected function afterSave()
    {
        parent::afterSave();
        $this->delivery_date = date('m/d/Y', $this->delivery_date);

        return true;
    }


    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($id = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        if ($id === null)
            $criteria->compare('id', $this->id);
        else
            $criteria->compare('id', $id);

        $criteria->compare('name', $this->name, true);
        $criteria->compare('name_t', $this->name_t, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('data', $this->data, true);
        $criteria->order = '`id` desc';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    //get picture fav list array
    public function getItemFavPictures()
    {

        $imagesDataPath = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogItem/' . $this->id;

        $favFilePath = $imagesDataPath . '/favData.php';
        $images = array();
        if (file_exists($favFilePath)) {
            $images = require($favFilePath);

        };

        return $images;

    }


  //get picture list array
  public function getItemPictures(){
    
      $imagesDataPath = Yii::getPathOfAlias('webroot').'/files/pictureBox/catalogItem/'.$this->id;
      $favFilePath = $imagesDataPath.'/data.php'; 
      $images = array();
     
      if (file_exists($favFilePath)){
          
           $images = require($favFilePath);
           if (isset($images['images']))
              return $images['images'];      
           else
               return array();
      } else {
  
          
           return array();
      }

  }       
  
  public function getItemWithMaximalPrice($catId)
  {

    $returnPrice = 0;

    $items = CatItemsToCat::model()->findAll(array(
      'select' => 'itemId',
      'condition' => 'catId = :catId',
      'params' => array(
          ':catId' => $catId
      ),
    ));


    foreach ($items as $item) {
      $itemModel =  $this->findByAttributes(array('id' => $item->itemId, 'published' => 1));

      if($returnPrice < $itemModel->price) $returnPrice = $itemModel->price;
    }


    return (int) $returnPrice;
  }

  //get path of one main picture, wich take from fav or common images list
  public function getItemMainPicture($tag = null)
  {


      $imagesDataPath = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogItem/' . $this->id;
      $favFilePath = $imagesDataPath . '/favData.php';

      $images = array();
      $itemImage = '';

      $images = $this->getItemFavPictures();
      if (count($images) != 0) {
          $imagesArray = array_values($images);
          $itemImage = $imagesArray[0];
      }
      if (count($images) == 0) {

          $images = $this->getItemPictures();
          if (count($images) > 0) {
              $imagesArray = array_values($images);
              $itemImage = $imagesArray[0];
          } else {
              return '#';
          }

      }

      if (is_null($tag)) {
          return array_shift($itemImage);
      } else {
          if (isset($itemImage[$tag]))
              return $itemImage[$tag];
          else
              return '#';
      }
  }

  
  public function combinedWithParser()
  {

      if (isset(Yii::app()->modules['parsers'])) {
        Yii::import('parsers.models.ParsersLinking');
        $model = ParsersLinking::model()->find("`toId`='" . $this->id . "'");

        if ($model) {
            return '<span class="icon icon-big icon-random"></span>';
        } else return "Нет";

      }

      return null;
  }

}

