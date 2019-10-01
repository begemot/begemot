<?php
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular.min.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular-resource.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular-sanitize.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/paging.js');
Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular-route.js');



Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/perform.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/perfomCtrl.angular.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/subTaskService.angular.js');


?>
<script>
    accessCode = "<?=$_REQUEST['accessCode']?>"
    app.value('accessCode', accessCode)
</script>
<div ng-app="performApp">
    <div ng-controller="performController" class="span12">

        <h1>{{taskData.name}}</h1>

        <ul class="nav nav-tabs">
            <li ng-class="{active:!$routeParams.tabName}">
                <a href="/contentTask/taskPerform/taskList?accessCode={{accessCode}}#!/">Информация</a>
            </li>
            <li ng-class="{active:$routeParams.tabName=='new'}"   ng-hide="recordsCount.new==0">
                <a href="/contentTask/taskPerform/taskList?accessCode={{accessCode}}#!/tab/new">Задачи <span class="label label-info">{{recordsCount.new}}</span></a>
            </li>
            <li ng-class="{active:$routeParams.tabName=='edit'}"   ng-hide="recordsCount.edit==0">
                <a href="/contentTask/taskPerform/taskList?accessCode={{accessCode}}#!/tab/edit">Начали работу <span class="label label-info">{{recordsCount.edit}}</span></a>
            </li>
            <li ng-class="{active:$routeParams.tabName=='mistake'}"   ng-hide="recordsCount.mistake==0">
                <a href="/contentTask/taskPerform/taskList?accessCode={{accessCode}}#!/tab/mistake">Правки <span class="label label-info">{{recordsCount.mistake}}</span></a>
            </li>

            <li ng-class="{active:$routeParams.tabName=='review'}"   ng-hide="recordsCount.review==0">
                <a href="/contentTask/taskPerform/taskList?accessCode={{accessCode}}#!/tab/review">Проверка <span class="label label-info">{{recordsCount.review}}</span></a>
            </li>
            <li ng-class="{active:$routeParams.tabName=='done'}"  ng-hide="recordsCount.done==0">
                <a href="/contentTask/taskPerform/taskList?accessCode={{accessCode}}#!/tab/done">Завершено <span class="label label-info">{{recordsCount.done}}</span></a>
            </li>
            <li ng-class="{active:$routeParams.tabName=='audit'}"  ng-hide="recordsCount.done==0">
                <a href="/contentTask/taskPerform/taskList?accessCode={{accessCode}}#!/tab/audit">Аудит</a>
            </li>
        </ul>
        <div ng-view></div>
        <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/'+$routeParams.tabName+'.html'">123</div>

<!--        <section ng-show="isBasePanelVisible" ng-cloak>-->
<!--       -->
<!--        </section>-->
<!--        <section ng-show="isTaskNewPanelVisible" ng-cloak>-->
<!--            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/newTasks.html'"></div>-->
<!--        </section>-->
<!--        <section ng-show="isTaskEditPanelVisible" ng-cloak>-->
<!--            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/editTasks.html'"></div>-->
<!--        </section>-->
<!--        <section ng-show="isTaskMistakePanelVisible" ng-cloak>-->
<!--            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/mistakeTasks.html'"></div>-->
<!--        </section>-->
<!--        <section ng-show="isTaskReviewPanelVisible" ng-cloak>-->
<!--            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/reviewTasks.html'"></div>-->
<!--        </section>-->
<!--        <section ng-show="isTaskDonePanelVisible" ng-cloak>-->
<!--            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/doneTasks.html'"></div>-->
<!--        </section>-->
    </div>
</div>