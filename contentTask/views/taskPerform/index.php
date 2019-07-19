<?php
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-resource.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-sanitize.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/paging.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/perform.js');

?>
<script>
    accessCode = "<?=$_REQUEST['accessCode']?>"
    app.value('accessCode', accessCode)
</script>
<div ng-app="performApp">
    <div ng-controller="performController" class="span6">

        <h1>{{taskData.name}}</h1>

        <ul class="nav nav-tabs">
            <li ng-class="{active:isBasePanelVisible}" ng-click="panel('base')">
                <a href="">Информация</a>
            </li>
            <li ng-class="{active:isTaskNewPanelVisible}"  ng-click="panel('taskNew')" ng-hide="recordsCount.new==0">
                <a href="">Задачи <span class="label label-info">{{recordsCount.new}}</span></a>
            </li>
            <li ng-class="{active:isTaskEditPanelVisible}"  ng-click="panel('taskEdit')" ng-hide="recordsCount.edit==0">
                <a href="">Начали работу <span class="label label-info">{{recordsCount.edit}}</a>
            </li>
            <li ng-class="{active:isTaskMistakePanelVisible}"  ng-click="panel('taskMistake')" ng-hide="recordsCount.mistake==0">
                <a href="">Правки <span class="label label-info">{{recordsCount.mistake}}</a>
            </li>

            <li ng-class="{active:isTaskReviewPanelVisible}"  ng-click="panel('taskReview')" ng-hide="recordsCount.review==0">
                <a href="">Проверка <span class="label label-info">{{recordsCount.review}}</a>
            </li>
            <li ng-class="{active:isTaskDonePanelVisible}"  ng-click="panel('taskDone')" ng-hide="recordsCount.done==0">
                <a href="">Завершено <span class="label label-info">{{recordsCount.done}}</a>
            </li>
        </ul>

        <section ng-show="isBasePanelVisible" ng-cloak>
            <h1>Информация</h1>
            <div ng-bind-html="taskData.text"></div>
            <h1>Дополнительно</h1>
            <button type="button" class="btn btn-success" ng-click="createElement()" ng-show="createBtnVisible">Создать позицию</button>
        </section>
        <section ng-show="isTaskNewPanelVisible" ng-cloak>
            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/newTasks.html'"></div>
        </section>
        <section ng-show="isTaskEditPanelVisible" ng-cloak>
            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/editTasks.html'"></div>
        </section>
        <section ng-show="isTaskMistakePanelVisible" ng-cloak>
            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/mistakeTasks.html'"></div>
        </section>
        <section ng-show="isTaskReviewPanelVisible" ng-cloak>
            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/reviewTasks.html'"></div>
        </section>
        <section ng-show="isTaskDonePanelVisible" ng-cloak>
            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/doneTasks.html'"></div>
        </section>
    </div>
</div>