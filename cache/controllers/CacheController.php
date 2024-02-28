<?php


class CacheController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }
    public function accessRules()
    {
        return array(

            array('allow', // allow admin user to perform 'admin' and 'delete' actions

                'actions' => array('getUniqueCacheGroups','getCacheDataPage','index','resetCacheForKey','resetAllCache','setValue','getValue'),

                'expression' => 'Yii::app()->user->canDo()'


            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionGetUniqueCacheGroups()
    {
        Yii::import('cache.models.Cache');
        $cacheGroups = Cache::model()->findAll(array(
            'select' => 'DISTINCT cache_group',
        ));

        $cacheGroupNames = array();
        foreach ($cacheGroups as $cacheGroup) {
            $cacheGroupNames[] = $cacheGroup->cache_group;
        }

        echo json_encode($cacheGroupNames);
    }

    public function actionGetCacheDataPage()
    {


      //  $page = $_REQUEST['page'];
//        $sortAttributes = array(
//           // 'id' => true,
//            'cache_group' => true,
//            'cache_key' => false,
//           // 'value' => false,
//        );
        $sortAttributes = CJSON::decode($_REQUEST['order']);
//        print_r($sortAttributes);
//        die();
        $sort = new CSort();
        if (is_array($sortAttributes) && count($sortAttributes)>0){
            $sort->attributes = array_keys($sortAttributes);

            $order = '';
            foreach ($sortAttributes as $attribute => $isAscending) {
                if (!empty($order)) {
                    $order .= ', ';
                }
                $order .= $attribute . ' ' . ($isAscending ? 'ASC' : 'DESC');
            }

            $sort->defaultOrder = $order;
        }


        Yii::import('cache.models.Cache');
        $model = new Cache('search');
        $model->unsetAttributes();

        if (isset($_REQUEST['search'])) {
            $model->attributes = CJSON::decode($_REQUEST['search']);
        }

        $criteria = new CDbCriteria;
        if ($model->id === null)
            $criteria->compare('id', $model->id);
        else
            $criteria->compare('id', $model->id);

        $criteria->compare('cache_group', $model->cache_group, true);
        $criteria->compare('cache_key', $model->cache_key, true);
      //  $criteria->compare('value', $model->value, true);


       // $criteria->order = '`id` desc';

        $dataProvider = new CActiveDataProvider($model, array(
            'pagination' => array(
                'pageSize' => $_REQUEST['perPage'], // Change the number of records displayed per page here
                'currentPage' => isset($_REQUEST['page']) ? $_REQUEST['page']-1  : 0, // Set the active page here

            ),
            'sort' => $sort,
            'criteria' => $criteria
        ));
        $models = $dataProvider->getData();
        $data = array();
        foreach ($models as $model) {
            $data[] = $model->attributes;
        }
        $pagination = $dataProvider->getPagination();
        $paginationOutput = array(
            'totalPages' => $pagination->getPageCount(),
            'currentPage' => $pagination->getCurrentPage(),
            'pageSize' => $pagination->getPageSize(),
            'itemCount' => $pagination->getItemCount(),
        );

        echo CJSON::encode([$data,$paginationOutput]);

    }



    public function actionIndex()
    {
        $this->layout = 'begemot.views.layouts.bs5clearLayout';


        $this->render('index');
    }

    // Reset cache for a specific key and group
    public function actionResetCacheForKey()
    {
        Yii::import('cache.models.Cache');

        $data = CJSON::decode( file_get_contents('php://input'));

        $group = $data['group'];
        $key = $data['key'];

        // Check if group and key are set
        if (!isset($group)) {
            throw new CHttpException(400, 'Group parameter is required');
        }
        if (!isset($key)) {
            throw new CHttpException(400, 'Key parameter is required');
        }
        $cache = new Cache;
        $cache->resetCacheForKey($group, $key);
        echo 'Cache reset for key ' . $key . ' in group ' . $group;
    }
    public function actionResetAllCache()
    {
        Yii::import('cache.models.Cache');
        Cache::model()->resetAllCache();

    }
    // Reset cache for a specific group
    public function actionResetCacheForGroup()
    {
        Yii::import('cache.models.Cache');
        $data = CJSON::decode( file_get_contents('php://input'));
        $group = $data['group'];
        // Check if group is set
        if (!isset($group)) {
            throw new CHttpException(400, 'Group parameter is required');
        }
        $cache = new Cache;
        $cache->resetCacheForGroup($group);
        echo 'Cache reset for group ' . $group;
    }

    // Set cache value
    public function actionSetValue()
    {
        $group = Yii::app()->request->getPost('group');
        $key = Yii::app()->request->getPost('key');
        $value = Yii::app()->request->getPost('value');

        // Check if group, key, and value are set
        if (!isset($group)) {
            throw new CHttpException(400, 'Group parameter is required');
        }
        if (!isset($key)) {
            throw new CHttpException(400, 'Key parameter is required');
        }
        if (!isset($value)) {
            throw new CHttpException(400, 'Value parameter is required');
        }

        $cache = new Cache;
        $cache->setCacheValue($group, $key, $value);
        echo 'Value set for key ' . $key . ' in group ' . $group;
    }

    // Get cache value
    public function actionGetValue()
    {
        $group = Yii::app()->request->getPost('group');
        $key = Yii::app()->request->getPost('key');

        // Check if group and key are set
        if (!isset($group)) {
            throw new CHttpException(400, 'Group parameter is required');
        }
        if (!isset($key)) {
            throw new CHttpException(400, 'Key parameter is required');
        }
        $cache = new Cache;
        $value = $cache->getCacheValue($group, $key);
        echo 'Value for key ' . $key . ' in group ' . $group . ': ' . $value;
    }
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Cache the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
