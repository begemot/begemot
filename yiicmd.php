#!/usr/bin/php
<?php
// yiicmd.php
// change the following paths if necessary
$yii 	=   realpath(dirname(__FILE__)) . "/../yiic";
$config =   realpath(dirname(__FILE__)) . '/../config/console.php';
 
// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
Yii::createConsoleApplication($config)->run();

?>
