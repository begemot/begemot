<?php
Yii::app()->clientScript->registerScriptFile("https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js");

Yii::app()->clientScript->registerScriptFile("/protected/modules/seo/assets/js/tagsProcess.js");
$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
?>
<h1>Анализ html-тегов сайта</h1>
<p>Делается после парсинга всех страниц сайта.</p>

<div ng-app="app" ng-controller="tagsProcess">
    <div>Процесс:{{executed}}/{{count}}</div>
    {{test}}
</div>
<?php


?>
