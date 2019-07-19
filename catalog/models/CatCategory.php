<?php

/**
 * This is the model class for table "catCategory".
 *
 * The followings are the available columns in table 'catCategory':
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property string $text
 * @property string $picSettings
 * @property integer $order
 * @property integer $dateCreate
 * @property integer $dateUpdate
 * @property integer $status
 * @property string $name_t
 */
class CatCategory extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CatCategory the static model class
     */

    public $categories;
    public $pubCategories;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**D `
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'catCategory';
    }

    public function behaviors()
    {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'dateCreate',
                'updateAttribute' => 'dateUpdate',
            ),
            'CBOrderModelBehavior' => array(
                'class' => 'begemot.extensions.order.BBehavior.CBOrderModelBehavior',

            ),
            'slug' => array(
                'class' => 'begemot.extensions.SlugBehavior',
            ),
        );
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
            array('pid, dateCreate, dateUpdate, status', 'numerical', 'integerOnly' => true),
            array('name, name_t', 'length', 'max' => 70),
            array('layout, viewFile, itemViewFile, seo_title', 'length', 'max' => 255),
            array('text,level,seo_title,published', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('name, text, picSettings,  dateCreate, dateUpdate, status, name_t', 'safe', 'on' => 'search'),
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
            'childCategories' => array(self::HAS_MANY, 'CatCategory', 'pid', 'order' => '`childCategories`.`order` ASC'),
            'childPublishedCategories' => array(self::HAS_MANY, 'CatCategory', 'pid', 'order' => '`childPublishedCategories`.`order` ASC', 'condition' => '`published`=1'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'pid' => 'Раздел родитель',
            'name' => 'Имя',
            'text' => 'Описание',
            'order' => 'Порядок',
            'dateCreate' => 'Date Create',
            'dateUpdate' => 'Date Update',
            'status' => 'Status',
            'name_t' => 'T Name',
            'published' => 'Публиковать на сайте'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($pid = -1)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;


        $criteria->compare('name', $this->name, true);
        $criteria->compare('pid', $pid, true);


        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => array('order' => false),
            ),
        ));
    }

    //Загружаем все категории в массив
    public function loadCategories()
    {


        $models = $this->findAll(array('order' => 'level desc'));


        $catsArray = [];
        $pubCatsArray = [];
        foreach ($models as $category) {
            $categoryArray = array();
            $categoryArray['id'] = $category->id;
            $categoryArray['pid'] = $category->pid;
            $categoryArray['name'] = $category->name;
            $categoryArray['order'] = $category->order;
            $categoryArray['level'] = $category->level;
            $categoryArray['name_t'] = $category->name_t;
            $categoryArray['model'] = $category;

            $catsArray[$category->id] = $categoryArray;
            if ($category->published) {
                $pubCatsArray[$category->id] = $categoryArray;
            }
        }

        $this->categories = $catsArray;
        $this->pubCategories = $pubCatsArray;

    }

    public function beforeSave()
    {
        $this->name_t = $this->mb_transliterate($this->name);
        if ($this->isNewRecord) {

            $this->orderBeforeSave();


        }
        if ($this->pid == -1) {
            $this->level = 0;
        } else {
            $parentCategory = CatCategory::model()->findAll(array('condition' => 'id = ' . $this->pid));
            $parentCategory = $parentCategory[0];
            $this->level = $parentCategory->level + 1;
        }
        return true;
    }

    public function afterDelete()
    {

        return true;
    }

    public function getCatArray()
    {

        if ($this->categories === null) {
            $this->loadCategories();
        }

        return $this->categories;
    }

    public function getPubCatArray()
    {
        if ($this->pubCategories === null) {
            $this->loadCategories();
        }

        return $this->pubCategories;
    }

    //Возвращает имя категории по id
    public function getCatName($id)
    {
        if ($id == -1) {
            return ' верхний уровень';
        }
        $categories = $this->getCatArray();

        if (isset($categories[$id]))
            return $categories[$id]['name'];
        else
            return false;
    }

    //Возвращаем категории входящие в раздел
    public function getCatChilds($id)
    {


        $array = $this->getCatArray();
        $resultArray = array();

        foreach ($array as $element) {
            if ($element['pid'] == $id) {
                $resultArray[] = $element;
            }

        }

        return $resultArray;//array_filter($this->getCatArray(),$filter );
    }

    //Возвращаем категории входящие в раздел
    public function getAllCatChilds($id)
    {

        $childs = $this->getCatChilds($id);
        $resultArray = $childs;
        if (count($childs) > 0) {

            foreach ($childs as $id => $child) {
                $tmpChildsArray = array();
                $tmpChildsArray = $this->getCatChilds($child['id']);
                $resultArray = array_merge($resultArray, $tmpChildsArray);
            }
        }
        return $resultArray;
    }

    public function getCatChildsCount($id)
    {
        return count($this->getCatChilds($id));
    }

    public function getCategory($id)
    {

        $categories = $this->getCatArray();
        return $categories[$id];
    }

    public function getPid($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }
        $categories = $this->getCatArray();
        return $categories[$id]['pid'];
    }

    public function getBreadCrumbs($id)
    {
        $breadCrumbs = array();
        if ($id != -1) {
            $activeElement = $this->getCategory($id);
            $breadCrumbs[] = $activeElement;
            while ($activeElement['pid'] != -1) {
                $activeElement = $this->getCategory($activeElement['pid']);
                $breadCrumbs[] = $activeElement;
                break;
            }
        }
        return array_reverse($breadCrumbs);
    }

    public function categoriesMenu()
    {

        $categories = $this->getCatArray();
      
        $menu = $categories;

        $menuEnd = array();
        foreach ($menu as $id => &$item) {

            $menuItem = array();

            $menuItem['label'] = $item['name'];
            $menuItem['url'] = array('catItemsToCat/admin', 'id' => $id);
            if ($item['pid'] == -1) {
                $menuEnd[$id] = $menuItem;

                foreach ($this->getAllCatChilds($id) as $item) {

                    $class = ($item['pid'] != $id) ? "sub-sub-item" : "sub-item";
                    $menuEnd += array($item['id'] => array(
                        'label' => $item['name'],
                        'url' => array('catItemsToCat/admin', 'id' => $item['id']),
                        'itemOptions' => array('class' => $class)
                    ));
                }
            }

        }


        return $menuEnd;
    }

    // public function insert(&$arr, $value, $index){
    //     $lengh = count($arr);
    //     if($index<0||$index>$lengh)
    //         return;

    //     for($i=$lengh; $i>$index; $i--){
    //         $arr[$i] = $arr[$i-1];
    //     }

    //     $arr[$index] = $value;
    // }

    //get picture fav list array
    public function getCatFavPictures()
    {

        $imagesDataPath = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogCategory/' . $this->id;

        $favFilePath = $imagesDataPath . '/favData.php';
        $images = array();
        if (file_exists($favFilePath)) {
            $images = require($favFilePath);
        };

        return $images;

    }

    //get picture list array
    public function getCatPictures()
    {
        $imagesDataPath = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogCategory/' . $this->id;

        $favFilePath = $imagesDataPath . '/data.php';
        $images = array();
        if (file_exists($favFilePath)) {
            $images = require($favFilePath);
        };
        if (isset($images['images'])) {
            return $images['images'];
        } else {
            return null;
        }
    }


    //get picture list array
    public function getCatVideos()
    {
        $imagesDataPath = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogCategoryVideo/' . $this->id;

        $favFilePath = $imagesDataPath . '/data.php';
        $images = array();
        if (file_exists($favFilePath)) {
            $images = require($favFilePath);
        };
        if (isset($images['images'])) {
            return $images['images'];
        } else {
            return null;
        }
    }

    //get path of one main picture, wich take from fav or common images list
    public function getCatMainPicture($tag = null)
    {

        $imagesDataPath = Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogCategory/' . $this->id;
        $favFilePath = $imagesDataPath . '/favData.php';

        $images = array();
        $catalogImage = '';

        $images = $this->getCatFavPictures();
        if (count($images) != 0) {
            $imagesArray = array_values($images);
            $catalogImage = $imagesArray[0];
        }

        if (count($images) == 0) {

            $images = $this->getCatPictures();
            if ($images != null && is_array($images)) {
                $imagesArray = array_values($images);
                $catalogImage = array_shift($imagesArray);
            } else {
                return '#';
            }

        }
        if (is_null($tag)) {
            return array_shift($catalogImage);
        } else {
            if (isset($catalogImage[$tag]))
                return $catalogImage[$tag];
            else
                return '#';
        }
    }

    public function getAllItems()
    {
        return $this->findAll(array(
            'select' => 'id, pid, name, level',
        ));
    }


    /*
    public static function nodetree($nodes) {
        $refs = array();
        $list = array();

        foreach ($nodes as $data) {
            $thisref = &$refs[ $data->id ];
            $thisref['pid'] = $data->pid;
            $thisref['name'] = $data->name;
            if ($data->pid == -1) {
                $list[ $data->id ] = &$thisref;
            } else {
                $refs[ $data->pid ]['children'][ $data->id ] = &$thisref;
            }
        }
        return $list;
    }

    /**
     * [Checks category item for having childs]
     * @param  [int]  $id
     * @return boolean
     *
    private function hasChilds($items, $id) {

       foreach ($items as $item) {
            if ($item['pid'] == $id)
                return true;
       }

       return false;
    }

    public static function getTreeChildsId($items) {
        $refs = array();
        $list = array();
        $ids = array();
        $parent = array();
        foreach ($items as $data) {
            $thisref = &$refs[ $data->id ];
            $thisref['id'] = $data->id;
            $thisref['pid'] = $data->pid;
            $thisref['name'] = $data->name;
            if ($data->pid == -1) {
                $list[ $data->id ] = &$thisref;
                if (CatCategory::model()->hasChilds($items, $data->id))
                    $parent[$data->id] = $data->id;
                // $id_[] = $data->id;
            } else {
                $list[$parent[$data->id]]['childs'];
                $ids[] = &$thisref;
            }
                $list[$parent[$data->id]]['childs'] = $ids;
            // $list[ $ ]['childs'] = $ids;
        }
            echo '<pre>';
                print_r($id_);
            echo '</pre>';
        return $list;
    }
     */
}