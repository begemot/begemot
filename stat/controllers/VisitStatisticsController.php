<?php
Yii::import('stat.models.*');
class VisitStatisticsController extends Controller
{
    public $layout = 'begemot.views.layouts.bs5clearLayout';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('index', 'getData', 'GetDailyStatistics', 'GetRecentVisits', 'FilterUserAgent', 'GetFilteredUserAgents', 'metrika','CheckMetrika'),
                'expression' => 'Yii::app()->user->canDo()'
            ),
            array(
                'deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionIndex()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'visit_time DESC'; // Сортировка по времени визита, новые визиты сверху

        $dataProvider = new CActiveDataProvider('VisitStatistics', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20, // Количество записей на страницу
            ),
        ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionGetData()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'visit_time DESC'; // Сортировка по времени визита, новые визиты сверху

        $visitStatistics = VisitStatistics::model()->findAll($criteria);

        $data = array();
        foreach ($visitStatistics as $visit) {
            $data[] = $visit->attributes;
        }

        echo CJSON::encode($data);
        Yii::app()->end();
    }

    public function actionGetDailyStatistics($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        } else {
            $date = date('Y-m-d', strtotime($date));
        }

        $blockedUserAgents = UserAgentFilters::model()->findAll();
        $blockedIPs = IpFilters::model()->findAll();

        $blockedUserAgentValues = array_map(function ($record) {
            return $record->userAgent;
        }, $blockedUserAgents);

        $blockedIPValues = array_map(function ($record) {
            return $record->ipAddress;
        }, $blockedIPs);

        $criteria = new CDbCriteria();
        $criteria->condition = 'DATE(visit_time) = :date';
        $criteria->params = array(':date' => $date);
        $criteria->addNotInCondition('user_agent', $blockedUserAgentValues);
        $criteria->addNotInCondition('ip_address', $blockedIPValues);

        $visitStatistics = VisitStatistics::model()->findAll($criteria);

        $statistics = array();
        foreach ($visitStatistics as $visit) {
            $domain = $visit->domain;
            $ip = $visit->ip_address;

            if (!isset($statistics[$domain])) {
                $statistics[$domain] = array(
                    'unique_ips' => array(),
                    'visits_count' => 0,
                );
            }

            if (!in_array($ip, $statistics[$domain]['unique_ips'])) {
                $statistics[$domain]['unique_ips'][] = $ip;
            }

            $statistics[$domain]['visits_count']++;
        }

        $result = array();
        foreach ($statistics as $domain => $data) {
            $result[] = array(
                'domain' => $domain,
                'unique_ips' => count($data['unique_ips']),
                'visits_count' => $data['visits_count'],
            );
        }

        echo CJSON::encode($result);
        Yii::app()->end();
    }

    public function actionGetRecentVisits()
    {
        $excludeFiltered = Yii::app()->request->getQuery('excludeFiltered', false);
        $limit = Yii::app()->request->getQuery('limit', 100);
        $site = Yii::app()->request->getQuery('site', '');

        $criteria = new CDbCriteria();
        $criteria->order = 'visit_time DESC';
        $criteria->limit = (int)$limit;

        if ($site) {
            $criteria->addCondition('domain = :site');
            $criteria->params[':site'] = $site;
        }

        if ($excludeFiltered) {
            $blockedUserAgents = UserAgentFilters::model()->findAll();
            $blockedIPs = IpFilters::model()->findAll();

            $blockedUserAgentValues = array_map(function ($record) {
                return $record->userAgent;
            }, $blockedUserAgents);

            $blockedIPValues = array_map(function ($record) {
                return $record->ipAddress;
            }, $blockedIPs);

            $criteria->addNotInCondition('user_agent', $blockedUserAgentValues);
            $criteria->addNotInCondition('ip_address', $blockedIPValues);
        }

        $visitStatistics = VisitStatistics::model()->findAll($criteria);

        $data = array();
        foreach ($visitStatistics as $visit) {
            $data[] = $visit->attributes;
        }

        echo CJSON::encode($data);
        Yii::app()->end();
    }



    public function actionFilterUserAgent()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        if (isset($request['userAgent'])) {
            $userAgent = $request['userAgent'];
            $model = new UserAgentFilters();
            $model->userAgent = $userAgent;
            if ($model->save()) {
                echo CJSON::encode(['status' => 'success']);
            } else {
                echo CJSON::encode(['status' => 'error', 'message' => 'Failed to save user agent.']);
            }
        } else {
            echo CJSON::encode(['status' => 'error', 'message' => 'Invalid request.']);
        }
        Yii::app()->end();
    }

    public function actionGetFilteredUserAgents()
    {
        $filteredUserAgents = UserAgentFilters::model()->findAll();
        $data = array_map(function ($record) {
            return $record->userAgent;
        }, $filteredUserAgents);

        echo CJSON::encode($data);
        Yii::app()->end();
    }

    public function actionMetrika()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'id ASC'; // Сортировка по ID, для примера

        $dataProvider = new CActiveDataProvider('Metrika', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20, // Количество записей на страницу
            ),
        ));

        $this->render('metrika', array(
            'dataProvider' => $dataProvider,
        ));
    }
    public function actionCheckMetrika($domain)
    {
        $metrika = Metrika::model()->findByAttributes(array('domain' => $domain));
        $result = array('hasMetrika' => $metrika !== null);
        echo CJSON::encode($result);
        Yii::app()->end();
    }
}
