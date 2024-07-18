<?php

class UserAgentFilters extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'userAgentFilters';
    }

    public function rules()
    {
        return array(
            array('userAgent', 'required'),
            array('userAgent', 'length', 'max'=>255),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'userAgent' => 'User Agent',
        );
    }

    public function getDbConnection()
    {
        return Yii::app()->commonDb;
    }

    public static function isBlockedUserAgent($userAgent)
    {
        return self::model()->exists('userAgent=:userAgent', array(':userAgent' => $userAgent));
    }
}
