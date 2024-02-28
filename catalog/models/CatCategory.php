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
    const deleted = 0;
    const normal = 1;
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
//            'CBOrderModelBehavior' => array(
//                'class' => 'begemot.extensions.order.BBehavior.CBOrderModelBehavior',
//
//            ),
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
            array('name, name_t, type', 'length', 'max' => 70),
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


        $models = $this->findAll(
            array(
                'order' => '`order`',
                'condition' => 'status="' . CatCategory::normal . '"'
            ));

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
            $categoryArray['type'] = $category->type;
//            $categoryArray['model'] = $category;

            $catsArray[$category->id] = $categoryArray;
            if ($category->published) {
                $pubCatsArray[$category->id] = $categoryArray;
            }
        }

        $this->categories = $catsArray;
        $this->pubCategories = $pubCatsArray;

    }
//    //TODO: новый алгоритм работы с категориями эту функцию умножает на ноль
//    public function getcategoriesTree(){
//        $getcategoriesTree = [];
//
//        if (!is_array($this->categories)){
//            $this->loadCategories();
//        }else {
//            $getcategoriesTree = $this->categories;
//            foreach ($getcategoriesTree as $key=>$cat){
//                if (isset($getcategoriesTree[$cat['pid']])){
//                    $getcategoriesTree[$cat['pid']]['childs'][] = $getcategoriesTree[$key];
//                    unset ($getcategoriesTree[$key]);
//                }
//            }
//            return $getcategoriesTree;
//        }
//
//
//    }

    public function beforeSave()
    {
        $this->name_t = $this->mb_transliterate($this->name);

        if ($this->isNewRecord) {
            $this->status = 1;
            // в зависимости от pid создаем в конец дерева или в конец поддерева

            $maxOrder = Yii::app()->db->createCommand('SELECT max(`order`) as max FROM `catCategory`')->queryScalar();
            $this->order = $maxOrder + 1;


//            $command = Yii::app()->db->createCommand('SELECT max(`order`) as max FROM `catCategory` where `pid`='.$this->pid)->queryRow();
//            if(!is_null($command['max'])){
//
//                $this->order =$command['max']+1;
//            } elseif ($this->pid==-1) {
//                //это первый корневой раздел
//                $this->order =1;
//            } else {
//                //$this->order = CatCategory::model()->findByPk($this->pid);
//                $this->order = CatCategory::model()->findByPk($this->pid)->order+1;
//
//            }
//
//            $criteria=new CDbCriteria;
//            $criteria->select='*';  // выбираем только поле 'title'
//            $criteria->condition='`order`>=:maxpidorder';
//            $criteria->params=array(':maxpidorder'=>$this->order);
//            $cats=CatCategory::model()->findAll($criteria);
//
//            foreach ($cats as $cat){
//                $cat->order =1+$cat->order;
//                $cat->save();
//            }


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

    private function deleteImages()
    {
        Yii::import('begemot.BegemotModule');
        $imagesDir = Yii::getPathOfAlias('webroot.files.pictureBox.catalogCategory') . '/' . $this->id;
        if (file_exists($imagesDir))
            BegemotModule::fullDelDir($imagesDir);
    }

    private function deleteAllCatToItems()
    {
        $models = CatItemsToCat::model()->findAllByAttributes([
            'catId' => $this->id
        ]);
        foreach ($models as $model) $model->delete();
    }

    public function beforeDelete()
    {
        $this->deleteImages();
        $this->deleteAllCatToItems();
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

    // Возвращаем дочерние категории, для которых текущий раздел является родительским.
    // Т.е. только один уровень.
    public function getCatChilds($id = null)
    {
        if (is_null($id)) $id = $this->id;

        $array = $this->getCatArray();

        $resultArray = array();

        foreach ($array as $element) {

            if ($this->id == $element['pid']) {

                $resultArray[] = $element;
            }

        }

        return $resultArray;//array_filter($this->getCatArray(),$filter );
    }

    //Возвращаем все дочерние категории входящие в раздел
    public function getAllCatChilds($id = null)
    {
        if (is_null($id)) $id = $this->id;

        $array = $this->getCatArray();

        $resultArray = array();
        $collectWasStarted = false;

        foreach ($array as $element) {

            if ($this->order < $element['order']) $collectWasStarted = true;
            if ($collectWasStarted /*&& !$collectWasStopped*/) {
                if ($this->level == $element['level']) {
                    break;
                }
                $resultArray[] = $element;
            }

        }

        return $resultArray;//array_filter($this->getCatArray(),$filter );
    }

    public function getCatChildsCount($id)
    {
        return count($this->getCatChilds($id));
    }

    public function getAllCatChildsCount($id)
    {
        return count($this->getAllCatChilds($id));
    }

    public function getMaxOrderOfSubTree($catId)
    {
        $catsOrderList = $this->categories;

        $minOrder = $catsOrderList[$catId]['order'];
        $level = $catsOrderList[$catId]['level'];

        foreach ($catsOrderList as $id => $item) {

            if ($item['order'] > $minOrder && $id != $catId && $level >= $item['level']) {
                $maxOrder = $item['order'] - 1;

                break;
            }
            $maxOrder = $item['order'];
        }

        return $maxOrder;
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
        foreach ($categories as &$item) {
            $item['name'] = str_repeat('--', $item['level']) . $item['name'];
        }

        $menu = $categories;

        $menuEnd = [];
        foreach ($menu as $id => &$item) {

            $menuItem = array();

            $menuItem['label'] = $item['name'];

            $menuItem['url'] = array('catItemsToCat/admin', 'id' => $id);
            $menuEnd[] = $menuItem;
//            if ($item['pid'] == -1) {
//                $menuEnd[$id] = $menuItem;
//
//                foreach ($this->getAllCatChilds($id) as $item) {
//
//                    $class = ($item['pid'] != $id) ? "sub-sub-item" : "sub-item";
//
//                    if ($item['type'] == 'base') {
//                        $subMenuUrl = array('catItemsToCat/admin', 'id' => $item['id']);
//                    } else {
//                        $subMenuUrl = array('catItemsToCat/schemaAdmin', 'id' => $item['id']);
//                    }
//
//                    $menuEnd += array($item['id'] => array(
//                        'label' => $item['name'],
//                        'url' => $subMenuUrl,
//                        'itemOptions' => array('class' => $class)
//                    ));
//                }
//            }

        }


        return $menuEnd;
    }


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

    /**
     * @param $draggedId id категории, которую перемещаем
     * @param $targetdId id категории, на которую перемещаем
     * @param $moveType 'middle' - сделать дочерним, 'left' - вставить перед целью, 'right' - вставить после
     */
    public static function moveTo($draggedId, $targetdId, $moveType)
    {
        $model = CatCategory::model();


        //Нужно проверить на перетаскивание на самого себя
        if ($draggedId == $targetdId) {
            //  $this->echoJsonCategories();
            return;
        }


//$tmp = $model->getcategoriesTree();
        $model->loadCategories();
        $catsOrderList = $model->categories;

        $minOrder = $catsOrderList[$draggedId]['order'];

        $maxOrder = $model->getMaxOrderOfSubTree($draggedId);

        //сколько разделов перемещаем
        $elementsForMoveCount = $maxOrder - $minOrder + 1;// ordermax-ordermin+1

        //Проверяем на перемещение на потомков самого себя
        foreach ($catsOrderList as $id => $item) {
            if ($item['order'] >= $minOrder && $item['order'] <= $maxOrder) {
                if ($item['id'] == $targetdId) {
                    //цель на которую перетащили является потомком того что перетаскивают
                    // $this->echoJsonCategories();
                    return;
                }
            }
        }

        //Выключаем разделы которые перемещаем
        foreach ($catsOrderList as $id => $item) {
            if ($item['order'] >= $minOrder && $item['order'] <= $maxOrder) {
                CatCategory::model()->updateByPk($id, ['status' => CatCategory::deleted]);
            }
        }


        //присваеваем новый порядок от 1 до n выключенным разделам
        $tmpCats = CatCategory::model()->findAll([
            'condition' => '`status`=' . CatCategory::deleted,
            'order' => '`order`'
        ]);

        $tmpOrder = 0;
        foreach ($tmpCats as $tmpCat) {
            $tmpOrder++;
            $tmpCat->order = $tmpOrder;
            $tmpCat->save();
        }


        //корректируем order не удаленных разделов
        $cats = CatCategory::model()->findAll([
            'condition' => '`order`>' . $maxOrder . ' and ' . '`status`=' . CatCategory::normal
        ]);

        foreach ($cats as $cat) {
            $cat->order = $cat->order - $elementsForMoveCount;
            $cat->save();
        }

        $targetCat = CatCategory::model()->findByPK($targetdId);
        $draggedCat = CatCategory::model()->findByPK($draggedId);


        if ($moveType == 'middle') {
            $draggedCat->pid = $targetCat->id;
            $draggedCat->save();
            $dLevel = $draggedCat->level - $targetCat->level;
        } elseif ($moveType == 'right' || $moveType == 'left') {
            $draggedCat->pid = $targetCat->pid;
            $draggedCat->save();
            $dLevel = $draggedCat->level - $targetCat->level + 1;
        }

        $tmpCats = CatCategory::model()->findAll([
            'condition' => '`status`=' . CatCategory::deleted,
            'order' => '`order`'
        ]);

        foreach ($tmpCats as $tmpCat) {
            $tmpCat->level = $tmpCat->level - $dLevel;
            $tmpCat->save();
        }

        //у $targetCat могут быть потомки
        $model->loadCategories();

        $childs = $model->getAllCatChilds($targetCat->id);
        $childsOrderAdd = 0;
        if (count($childs) > 0) {
            $childsOrderAdd = count($childs);
        }

        if ($moveType != 'left') {
            $tmpCats = CatCategory::model()->findAll([
                'condition' => '`status`=' . CatCategory::normal . ' and `order`>' . ($targetCat->order + $childsOrderAdd),
                'order' => '`order`'
            ]);
        } else {
            $tmpCats = CatCategory::model()->findAll([
                'condition' => '`status`=' . CatCategory::normal . ' and `order`>=' . $targetCat->order,
                'order' => '`order`'
            ]);
        }


        foreach ($tmpCats as $tmpCat) {
            $tmpCat->order = $tmpCat->order + $elementsForMoveCount;

            $tmpCat->save();
        }

        $tmpCats = CatCategory::model()->findAll([
            'condition' => '`status`=' . CatCategory::deleted,
            'order' => '`order`'
        ]);


        foreach ($tmpCats as $tmpCat) {

            if ($moveType != 'left') {
                $tmpCat->order = $tmpCat->order + $targetCat->order + $childsOrderAdd;

            } else {
                $tmpCat->order = $tmpCat->order + $targetCat->order;
                if ($moveType == 'left') $tmpCat->order--;
            }


            $tmpCat->status = CatCategory::normal;
            $tmpCat->save();
        }

        $model = CatCategory::model();
    }


}
