<?php
class Cache extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'Cache';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('cache_group, cache_key', 'required'),
            array('cache_group', 'length', 'max' => 255),
            array('value', 'unsafe'),
            array('id,cache_group, cache_key,value', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(

        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // define any relational rules here
        );
    }

    public function getValue($group, $key)
    {
        Yii::import('settings.components.CSettings');
        $settings = new CSettings('cache');
        if(!$settings->settings['enable']) return false;

        $cache = Yii::app()->cache;
        $cacheKey = $this->getCacheKey($group, $key);

        // Try to retrieve the value from the cache
        $value = $cache->get($cacheKey);

        if ($value === false) {
            // If the value isn't in the cache, retrieve it from the database
            $cacheRow = $this->find('cache_group=:group AND cache_key=:key', array(':group'=>$group, ':key'=>$key));
            if ($cacheRow !== null) {
                $value = unserialize($cacheRow->value);
                // Store the value in the cache for next time
                $cache->set($cacheKey, $value);
            }
        }

        return $value;
    }

    public function setValue($group, $key, $value)
    {

        // Store the value in the database
        $cacheRow = $this->find('cache_group=:group AND cache_key=:key', array(':group'=>$group, ':key'=>$key));
        if ($cacheRow === null) {
            $cacheRow = new Cache;
            $cacheRow->cache_group = $group;
            $cacheRow->cache_key = $key;
        }

        $cacheRow->value = serialize($value);
        if(!$cacheRow->save()){


            throw  new Exception(var_export($cacheRow->errors,true));
        }

        // Store the value in the cache
        $cache = Yii::app()->cache;
        $cacheKey = $this->getCacheKey($group, $key);
        $cache->set($cacheKey, $value);
    }

    protected function getCacheKey($group, $key)
    {
        return 'Cache_' . $group . '_' . $key;
    }

    public function resetCacheForGroup($group)
    {
        $cacheRows = $this->findAll('cache_group=:group', array(':group'=>$group));
        foreach ($cacheRows as $cacheRow) {
            $cacheKey = $this->getCacheKey($group, $cacheRow->cache_key);
            Yii::app()->cache->delete($cacheKey);
            $cacheRow->delete();
        }
    }

    public function resetCacheForKey($group, $key)
    {
        $cacheKey = $this->getCacheKey($group, $key);
        if (Yii::app()->cache->delete($cacheKey))
            throw  Exception('Yii::app()->cache->delete fail');
        $cacheRow = $this->find('cache_group=:group AND cache_key=:key', array(':group'=>$group, ':key'=>$key));
        if ($cacheRow !== null) {
            $cacheRow->delete();
        }

    }

    public function resetAllCache()
    {

        $cacheRows = $this->findAll();
        foreach ($cacheRows as $cacheRow) {
            $cacheKey = $this->getCacheKey($cacheRow->cache_group, $cacheRow->cache_key);
            Yii::app()->cache->delete($cacheKey);
            $cacheRow->delete();
        }
    }


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
}