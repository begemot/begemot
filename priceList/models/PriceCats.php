<?php

/**
 * This is the model class for table "priceCats".
 *
 * The followings are the available columns in table 'priceCats':
 * @property integer $id
 * @property string $name
 * @property integer $order
 */
class PriceCats extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'priceCats';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 200),
            array('pid','safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, order', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors()
    {
        $behaviors = array(

            'CBOrderModelBehavior' => array(
                'class' => 'begemot.extensions.contentKit.behavior.CBOrderModelBehavior',
            )
        );

        return array_merge($behaviors, parent::behaviors());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'subCats' => array(self::HAS_MANY, 'PriceCats', array('pid' => 'id')),
            'prices' => array(self::HAS_MANY, 'Prices', array('catId' => 'id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'order' => 'Order',
            'pid'=>'Родительский раздел'
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('order', $this->order);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PriceCats the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeDelete(){
        parent::beforeDelete();


        $catsForDelete = $this->subCats();
        if (count ($catsForDelete)==0) return;

        foreach ($catsForDelete as $catForDelete){
            $this->deletePricesOfCat($catForDelete);
//            print_r($catForDelete->name);
            $catForDelete->delete();
            PriceCats::model()->deleteByPk($catForDelete->id);

        }

        $this->deletePricesOfCat($this);

        return true;
    }

    public function deletePricesOfCat ($catModel){
//        $category = PriceCats::model()->findByPk($catId);
        $prices = $catModel->prices;
        foreach ($prices as $price){
            $price->delete();
        }
    }

    public function beforeSave()
    {
        parent::beforeSave();


        if ($this->pid>0){
            $this->level=1;
        } else {
            $this->level=0;
        }


        if ($this->isNewRecord) {

            $criteria = new CDbCriteria;

            $criteria->select = 'MAX(`order`) as `order`';


            $order = $this->find($criteria);

            $this->order = $order->order + 1;

        }

        return true;
    }

}
