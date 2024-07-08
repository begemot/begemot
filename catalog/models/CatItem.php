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
Yii::import('catalog.models.*');
Yii::import('webroot.protected.modules.schema.components.*');
class CatItem extends ContentKitModel
{


    private $schemaArray = null;

    public function schemaGet($fieldId)
    {
        $schemaType = 'catItem';
        $groupId = $this->id;
        $link = new CSchemaLink($schemaType, $groupId);
        return $link->get($fieldId);
    }
    public function schemaSet($fieldId, $value, $dataType = 'String')
    {

        $schemaType = 'catItem';
        $groupId = $this->id;

        $link = new CSchemaLink($schemaType, $groupId);

        $link->setData($fieldId, $value, $dataType);
    }
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
            array('name, name_t, article', 'length', 'max' => 255),
            array('seo_title', 'length', 'max' => 255),
            // The following rule is used by search().
            array('id, name, name_t, status, data, price, text, name, delivery_date, quantity, authorId', 'safe'),
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
            'reviews' => array(self::HAS_MANY, 'Reviews', 'pid', 'condition' => 'status=1'),
            'options' => array(self::MANY_MANY, 'CatItem', 'catItemsToItems(itemId, toItemId)', 'order' => '`options_options`.`order`'),
            'colors' => array(self::HAS_MANY, 'CatColorToCatItem', 'catItemId'),
        );
    }

    public function getVideos()
    {

        $imagesDataPath = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogItemVideo/' . $this->id;
        $favFilePath = $imagesDataPath . '/data.php';
        $images = array();

        if (file_exists($favFilePath)) {

            $images = require($favFilePath);
            if (isset($images['images']))
                return $images['images'];
            else
                return array();
        } else {


            return array();
        }
    }

    public function getCategoriesItemIn()
    {
        $categories = CatItemsToCat::model()->with('cat')->findAll(array("condition" => '`t`.`itemId`=' . $this->id . ''));

        return $categories;
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

    public function isPublished()
    {
        $checked = ($this->published) ? "checked" : "";
        echo "<input type='checkbox' {$checked} class='togglePublished' data-id= '{$this->id}'>";
    }

    public function removeFromCategory()
    {

        //        echo "<input type='checkbox' {$checked} class='togglePublished' data-id= '{$this->id}'>";
        echo "<a  data-id= '{$this->id}'  class='removeBtn btn btn-primary btn-mini'>Убрать из раздела</a>";
    }

    public static function getSale($id, $type = '')
    {


        if ($listOfCategoryDiscounts = DiscountRelation::model()->with("discount")->findAll(array("condition" => "type=1"))) {
            foreach ($listOfCategoryDiscounts as $categoryDiscount) {
                if (CatItemsToCat::model()->find('catId=:catId AND itemId=:itemId AND active=1', array(':catId' => $categoryDiscount->targetId, ":itemId" => $id))) {
                    return $categoryDiscount->discount->sale;
                }
            }
        }

        $saleOfItem = DiscountRelation::model()->with("discount")->find(array("condition" => "type=2 AND active=1 AND targetId=" . $id));

        if ($saleOfItem) return $saleOfItem->discount->sale;

        return false;
    }

    public static function getMinSale($id)
    {
        if ($listOfCategoryDiscounts = DiscountRelation::model()->with("discount")->findAll(array("condition" => "type=1"))) {
            foreach ($listOfCategoryDiscounts as $categoryDiscount) {
                if (CatItemsToCat::model()->find('catId=:catId AND itemId=:itemId AND active=1', array(':catId' => $categoryDiscount->targetId, ":itemId" => $id))) {
                    return $categoryDiscount->discount->minSale;
                }
            }
        }

        $saleOfItem = DiscountRelation::model()->with("discount")->find(array("condition" => "type=2 AND active=1 AND targetId=" . $id));

        if ($saleOfItem) return $saleOfItem->discount->minSale;

        return false;
    }

    public static function getMaxSale($id)
    {
        if ($listOfCategoryDiscounts = DiscountRelation::model()->with("discount")->findAll(array("condition" => "type=1"))) {
            foreach ($listOfCategoryDiscounts as $categoryDiscount) {
                if (CatItemsToCat::model()->find('catId=:catId AND itemId=:itemId AND active=1', array(':catId' => $categoryDiscount->targetId, ":itemId" => $id))) {
                    return $categoryDiscount->discount->maxSale;
                }
            }
        }

        $saleOfItem = DiscountRelation::model()->with("discount")->find(array("condition" => "type=2 AND active=1 AND targetId=" . $id));

        if ($saleOfItem) return $saleOfItem->discount->maxSale;

        return false;
    }

    public function isTop()
    {
        $checked = ($this->top) ? "checked" : "";
        echo "<input type='checkbox' {$checked} class='toggleTop' data-id= '{$this->id}'>";
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Наименование',
            'name_t' => 'Наименование транслитом',
            'status' => 'Статус',
            'data' => 'Дата',
            'delivery_date' => 'Дата поставки:',
            'quantity' => 'Количество',
            'authorId' => 'Автор'
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
                if (isset($_REQUEST['CatItem'][$itemRow->name_t])) {
                    if (is_array($_REQUEST['CatItem'][$itemRow->name_t])) {
                        $this->$paramName = implode(',', $_REQUEST['CatItem'][$itemRow->name_t]);
                    } else $this->$paramName = $_REQUEST['CatItem'][$itemRow->name_t];
                }
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
        $criteria->compare('status', $this->status, true);
        $criteria->compare('article', $this->article, true);
        // $criteria->compare('data', $this->data, true);
        $criteria->order = '`id` desc';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array('pageSize' => 50),
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
    public function getItemPictures()
    {
        Yii::import("pictureBox.components.PBox");
        $PBox = new PBox("catalogItem", $this->id);

        $images = $PBox->getSortedImageList();

        return $images;
    }

    public function getItemWithMaximalPrice($catId)
    {
        $returnPrice = 0;


        $criteria = new CDbCriteria;
        $criteria->select = 'max(i.price) as maxprice';
        $criteria->with = array('item' => array('alias' => 'i'));
        $criteria->condition = 't.catId = :catId AND i.published = :published';
        $criteria->params = array(':catId' => $catId, ':published' => 1);
        $price = CatItemsToCat::model()->find($criteria);


        if (isset($price->maxprice)) $returnPrice = $price->maxprice;

        return (int)$returnPrice;
    }

    //get path of one main picture, wich take from fav or common images list
    public function getItemMainPicture($tag = null)
    {

        Yii::import("pictureBox.components.PBox");
        $PBox = new PBox("catalogItem", $this->id);
        $image = $PBox->getFirstImage($tag);
        return $image;

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

    public function searchInModel($queryWord)
    {
        $queryWord = addcslashes($queryWord, '%_'); // escape LIKE's special characters
        $criteria = new CDbCriteria(array(
            'condition' => "name LIKE :match",
            'params' => array(':match' => "%$queryWord%")
        ));

        $items = CatItem::model()->findAll($criteria);

        return $items;
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

    public function createColorAndAddImages($array)
    {


        Yii::import('catalog.models.*');

        foreach ($array as $color) {
            if ($color == 'no colors') continue;
            if (!isset($color['title']) || !isset($color['code'])) {
                throw new Exception('не существует title или code');
            }
            $title = $color['title'];
            $code = $color['code'];
            if (!($colorToItemId = CatColor::isCatItemHasColorByTitle($this->id, $title))) {

                $colorToItemId = $this->addNewColor($title, $code);
            }

            foreach ($color['images'] as $image) {
                $this->addImageToColor($colorToItemId, $image);
            }
        }
    }

    public function addNewColor($title, $code)
    {
        Yii::import('catalog.models.*');
        return CatColor::createColor($title, $code, $this->id);
    }

    public function addImageToColor($colorToItemId, $image)
    {
        Yii::import('catalog.models.*');
        return CatColor::addImageToColor($colorToItemId, $image);
    }


    public function getStandartCategories()
    {
        $standartCatsArrayNames = ['stock', 'catalog', 'sold', 'archive'];
        return CatCategory::model()->findAllByAttributes(['name' => $standartCatsArrayNames]);
    }

    public function clearStandartCategories()
    {

        $standartCatsIds = array_column($this->getStandartCategories(), 'id');


        $catItemsToCatArray = CatItemsToCat::model()->findAllByAttributes(['itemId' => $this->id, 'catId' => $standartCatsIds]);
        array_map(function ($item) {
            $item->delete();
        }, $catItemsToCatArray);
        // print_r($catItemsToCat);
    }

    public function moveToStandartCat($name)
    {
        $this->clearStandartCategories();

        if ($name == 'clear') {
            return;
        }
        
        $catItemToCat = new CatItemsToCat();
        $catItemToCat->itemId = $this->id;

        $cat = CatCategory::model()->findAllByAttributes(['name' => $name]);

        $catItemToCat->catId = $cat[0]->id;
        $catItemToCat->save();
    }

    // public function moveToStock(){
    //     $this->moveToStandartCat('stock');
    // }
    // public function moveToArchive(){
    //     $this->moveToStandartCat('archive');
    // }
    // public function moveToCatalog(){
    //     $this->moveToStandartCat('catalog');
    // }
    // public function moveToSold(){
    //     $this->moveToStandartCat('sold');
    // }
    public function isInArchive(){
        $result = CatItemsToCat::model()->findAllByAttributes(['itemId'=>$this->id]);

        if($result){
            foreach($result as $catItemToCat){
                if($catItemToCat->cat->name=='archive') return true;
            }
        }

    }

    public function isInStock(){}
    public function isInSold(){
        
        $result = CatItemsToCat::model()->findAllByAttributes(['itemId'=>$this->id]);
        
        if($result){
            foreach($result as $catItemToCat){
                if($catItemToCat->cat->name=='sold') return true;
            }
        }

    }

    public function isInCatalog(){}
}
