<?php

class IpFilters extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ipFilters';
    }

    public function rules()
    {
        return array(
            array('ipAddress', 'required'),
            array('ipAddress', 'length', 'max'=>45),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'ipAddress' => 'IP Address',
        );
    }

    public  function getDbConnection()
    {
        return Yii::app()->commonDb;
    }

    public static function isBlockedIpAddress($ipAddress)
    {
        return self::model()->exists('ipAddress=:ipAddress', array(':ipAddress' => $ipAddress));
    }
}
