<?php

/**
 * This is the model class for table "CatItemsToCat".
 *
 * The followings are the available columns in table 'CatItemsToCat':
 * @property integer $catId
 * @property integer $itemId
 * @property integer $order
 */
class CatItemsToCat extends CActiveRecord
{

    public $item_name;
    public $maxprice;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CatItemsToCat the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function behaviors()
    {
        return array(
            'CBOrderModelBehavior' => array(
                'class' => 'begemot.extensions.contentKit.behavior.CBOrderModelBehavior',
            )
        );
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'CatItem', 'itemId'),
            'cat' => array(self::BELONGS_TO, 'CatCategory', 'catId'),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'catItemsToCat';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('catId, itemId', 'required'),
            array('catId, itemId', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('catId, itemId, item_name', 'safe', 'on' => 'search'),
        );
    }

    public function search($id = null)
    {
        $criteria = new CDbCriteria;
        $criteria->with = 'item';
        $criteria->condition = '`t`.`catId`=' . $id . '';
        $criteria->compare('itemId', $this->itemId, true);
        $criteria->compare('catId', $this->catId, true);
        $criteria->compare('item.name', $this->item_name, true);
        $criteria->order = 't.order';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function beforeSave()
    {
        if ($this->isNewRecord) {
            $result = count($this->model()->findAll(array('condition' => 'catId =' . $this->catId . ' and itemId=' . $this->itemId)));

            if ($result != 0)
                return false;
            else {

                $orderMax = Yii::app()->db->createCommand()->select('max(`order`)')->where('`catId`="' . $this->catId . '"')->from('catItemsToCat')->queryScalar();

                $this->order = $orderMax + 1;
                return true;
            }
        }
        return true;
    }

    public function getItemMainPicture()
    {
        if (!is_null($this->item)) {

            return $this->item->getItemMainPicture("admin");
        } else $this->selfDestroy();
    }

    public function getItemId()
    {
        if (!is_null($this->item)) {

            return $this->item->id;
        } else $this->selfDestroy();
    }

    public function getItemName()
    {
        if (!is_null($this->item)) {

            return $this->item->name;
        } else $this->selfDestroy();
    }

    public function isPublished()
    {
        if (!is_null($this->item)) {

            return $this->item->isPublished();
        } else $this->selfDestroy();
    }

    public function isTop()
    {
        if (!is_null($this->item)) {

            return $this->item->isTop();
        } else $this->selfDestroy();
    }
    public function getName_t()
    {
        if (!is_null($this->item)) {

            return $this->item->name_t;
        } else $this->selfDestroy();
    }



    private function selfDestroy()
    {
         $this->delete();
    }
}
