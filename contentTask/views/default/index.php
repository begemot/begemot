<?php

Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-resource.js');

/* @var $this DefaultController */
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/tasks.js');


$this->breadcrumbs = array(
    $this->module->id,
);
?>
<h1><?php echo $this->uniqueId . '/' . $this->action->id; ?></h1>
<div ng-app="app">
    <div ng-controller="mainCtrl">
        <test-directive task-id="4"></test-directive>
        <table class="table">
            <tr ng-repeat="task in tasks">
               <td>{{task.id}}</td> <td>{{task.name}}</td>
            </tr>
        </table>
    </div>
</div>

