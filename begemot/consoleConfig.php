<?php
// protected/config/console.php
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'My Console Application',
 
    'import'=>array(
        //'application.models.*',
        'application.modules.parsers.models.ParsersLinking',
        'application.modules.parsers.models.ParsersStock',
        'application.modules.catalog.models.CatItem',
    'application.modules.catalog.models.CatItemsRow',
        'application.modules.catalog.components.*',
        'application.modules.crontabs.components.*',
        'application.modules.begemot.commands.ParseCommand',
        'application.modules.jobs.components.*', // components for jobs
        'application.jobs.*',
    'application.moodules.parsers.*',
    ),

    'commandMap' => array(
      'parse'=>array(
         'class'=>'application.modules.begemot.commands.ParseCommand',
      ),
    ),

    'modules' => array(
        'begemot',
        'jobs',
    ),
 
    // application components
    'components'=>array(

        'db'=>array(
        'connectionString' => 'mysql:host=localhost;dbname=database_name',
        'emulatePrepare' => true,
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8',
    ),
    
    'log'=>array(
        'class'=>'CLogRouter',
        'routes'=>array(
            array(
                'class'=>'CFileLogRoute',
                'levels'=>'error, warning',
            ),
        ),
    ),
                // usefull for generating links in email etc...
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName' => FALSE,
            'rules'=>array(),
        ),          
    ),

    'params' => array(
        // this is used in contact page
        'adminEmail' => 'scott2to@gmail.com',
        'website' => 'http://rosvezdehod.ru',
    ),
);