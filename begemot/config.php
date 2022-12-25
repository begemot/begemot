<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../..',

    'name' => 'My Web Application',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'layout' => 'clearNoAnimate',
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.modules.begemot.extensions.giix.components.*',
        'application.modules.begemot.components.NestedDynaTree.*',
        'application.modules.begemot.extensions.crontab.*',
        'application.modules.begemot.extForBaseClasses.*',
    ),
    'language' => 'ru',

    'modules' => array(
        'begemot',
    ),

    'components' => array(


        // uncomment the following to enable URLs in path-format

        'urlManager' => array(

            'urlFormat' => 'path',
            'showScriptName' => false,
            'caseSensitive' => true,
            'urlSuffix' => '.html',
            'rules' => array(

                'index' => '/',
                '/admin' => '/begemot',


                '/contact' => '/site/contact/',

                'promo_<promoId:\d+>' => 'catalog/site/promoView',



                'contacts' => '/site/contact',
             //   'katalog' => '/site/ModelsAndPrices',

            )

        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cronLog.log',
                    'maxLogFiles' => 2,
                    'categories' => 'cron'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'webParser.log',
                    'maxLogFiles' => 2,
                    'categories' => 'webParser'
                ),

            ),
        ),

        'cache' => array(
            'class' => 'system.caching.CFileCache',
        ),

    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'scott2to@gmail.com',
        'RestfullYii' => [
            'req.auth.user' => function ($application_id, $username, $password) {
                return true;
            },
        ]
    ),
);
