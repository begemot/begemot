<?php

/**
 * This is the model class for table "companyDepart".
 *
 * The followings are the available columns in table 'companyDepart':
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property string $titleSeo
 * @property string $nameT
 */

Yii::import('begemot.extensions.contentKit.ContentKitModel');

class CompanyDepart extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'companyDepart';
    }

    public function behaviors()
    {
        $behaviors = array(
            'slug' => array(
                'class' => 'begemot.extensions.SlugBehavior',
            ),
            'CBOrderModelBehavior' => array(
                'class' => 'begemot.extensions.contentKit.behavior.CBOrderModelBehavior',
            )
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
        return array(
            array('name', 'length', 'max' => 45),
            array('titleSeo, nameT', 'length', 'max' => 200),
            array('text', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, text, titleSeo, nameT', 'safe', 'on' => 'search'),
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
            'emps' => array(self::MANY_MANY, 'CompanyEmployee', 'companyEmpToDep(depId,empId)'/*,'order'=>'options_options.order ASC'*/),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Название подразделения',
            'text' => 'Описание',
            'titleSeo' => 'Title Seo - для поисковой оптимизации',
            'nameT' => 'Латинскими буквами название подразделения, будет использовано при формировании ссылки.(можно не вводить, будет создано автоматически из названия)',
        );
    }

    public function beforeSave()
    {
        parent::beforeSave();

        $this->nameT = $this->mb_transliterate($this->name);


        if ($this->isNewRecord) {

            $criteria = new CDbCriteria;

            $criteria->select = 'MAX(`order`) as `order`';


            $order = $this->find($criteria);

            $this->order = $order->order + 1;

        }

        return true;
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
        $criteria->compare('text', $this->text, true);
        $criteria->compare('titleSeo', $this->titleSeo, true);
        $criteria->compare('nameT', $this->nameT, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CompanyDepart the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
