<?php

/**
 * This is the model class for table "tasks_invoice".
 *
 * The followings are the available columns in table 'tasks_invoice':
 * @property integer $id
 * @property string $description
 * @property double $amount
 * @property string $create_at
 * @property string $paid_at
 * @property integer $user_id
 * @property integer $task_id
 */
class TasksInvoice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tasks_invoice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, create_at, user_id, task_id', 'required'),
			array('user_id, task_id', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('description', 'length', 'max'=>255),
			array('paid_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, description, amount, create_at, paid_at, user_id, task_id', 'safe', 'on'=>'search'),
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
			'description' => 'Description',
			'amount' => 'Amount',
			'create_at' => 'Create At',
			'paid_at' => 'Paid At',
			'user_id' => 'User',
			'task_id' => 'Task',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('create_at',$this->create_at,true);
		$criteria->compare('paid_at',$this->paid_at,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('task_id',$this->task_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TasksInvoice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
