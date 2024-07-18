<?php

class VisitStatisticsComponent extends CApplicationComponent
{
    public function recordVisit()
    {
        $ip_address = Yii::app()->request->userHostAddress;
        $user_agent = Yii::app()->request->userAgent;
        $page_visited = Yii::app()->request->requestUri;
        $domain = Yii::app()->request->serverName;

        // Выводим отладочную информацию для проверки
        // echo "Recording visit: IP = $ip_address, User Agent = $user_agent, Page = $page_visited, Domain = $domain";

        $command = Yii::app()->commonDb->createCommand();
        $command->insert('visit_statistics', [
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'visit_time' => new CDbExpression('NOW()'),
            'page_visited' => $page_visited,
            'domain' => $domain,
        ]);
    }
}
