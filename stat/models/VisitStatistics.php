<?php

class VisitStatistics extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'visit_statistics';
    }

    public function rules()
    {
        return array(
            array('ip_address, user_agent, page_visited, domain', 'required'),
            array('ip_address', 'length', 'max'=>45),
            array('page_visited, domain', 'length', 'max'=>255),
            array('visit_time', 'safe'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'visit_time' => 'Visit Time',
            'page_visited' => 'Page Visited',
            'domain' => 'Domain',
        );
    }

    public  function getDbConnection()
    {
        return Yii::app()->commonDb;
    }

    public static function isBlockedIpAddress($ipAddress)
    {
        return IpFilters::model()->isBlockedIpAddress($ipAddress);
    }

    public static function isBlockedUserAgent($userAgent)
    {
        return UserAgentFilters::model()->isBlockedUserAgent($userAgent);
    }
}
