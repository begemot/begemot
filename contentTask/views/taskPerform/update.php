<?php
Yii::app()->clientScript->registerScriptFile('https://cdn.ckeditor.com/4.5.6/standard/ckeditor.js');

Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-resource.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-sanitize.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/paging.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/perform.js');

?>
<script>
    accessCode = "<?=$_REQUEST['accessCode']?>"
    subtaskId = "<?=$_REQUEST['id']?>"

    app.value('accessCode', accessCode)
    app.value('subtaskId', subtaskId)
</script>
<style>
    a.active {
        color: grey;
    }

    a.active:hover {
        cursor: default;
        text-decoration: none;
    }
</style>
<div ng-app="performApp">
    <div ng-controller="edit">
        <div class="row">
            <ul class="nav nav-tabs">
                <li ng-class="{active:panels.base}" ng-click="panel('base')" ng-show="visible.pageBase">
                    <a href="">Исходные</a>
                </li>
                <li ng-class="{active:panels.current}" ng-click="panel('current')" ng-show="visible.pageCurrent">
                    <a href="">Текущая работа</a>
                </li>
                <li ng-class="{active:panels.review}" ng-click="panel('review')" ng-show="(currentIteration>0) || (isAdmin == true && (subtaskStatus=='review'))">
                    <a href="">Правки</a>
                </li>
                <li>
                    <div style="margin: 5px 5px" class="btn-group" ng-show="visible.btnSave">
                        <a class="btn btn-primary" href="" ng-click="save()"> {{saveText}}</a>
                    </div>
                    <div style="margin: 5px 5px" class="btn-group" ng-show="visible.btnSendForReview">
                        <a class="btn btn-warning" href="" ng-click="sendToReview()"> {{reviewText}}</a>
                    </div>
                </li>
            </ul>

        </div>

        <div ng-show="visible.dataDiv">

            <div class="row">
                <div class="hero-unit span8" ng-show="visible.infoReview">
                    <h1>Работа на проверке</h1>
                    <p>Это подзадание отправленно на проверку администратором. После проверки задание либо вернется в
                        работу
                        и появится на вкладке "Правки", либо появится во вкладке "Завершено".
                        Что бы ускорить проверку свяжитесь с человеком, который должен принять у вас работу.
                    </p>

                </div>
            </div>
            <div class="tabbable tabs-left">
                <ul class="nav nav-tabs">
                    <li ng-class="{active:item.name==dataActiveTab}" ng-repeat="(index,item) in visibleData"><a href="#{{item.name}}" data-toggle="tab">{{item.name}}</a></li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane" ng-class="{active:item.name==dataActiveTab}" id="{{item.name}}" ng-repeat="(index,item) in visibleData">

                        <div >
                            <div class="">
                                <h3>Поле "{{item.name}}"</h3>
                            </div>
                            <div class="span1" style="text-align: right">
                                <a href="" ng-class="{'label label-info':editors[item.name]=='simple'}"
                                   ng-click="editorView(item.name,'simple')">Поле</a> <br>
                                <a href="" ng-class="{'label label-info':editors[item.name]=='textarea'}"
                                   ng-click="editorView(item.name,'textarea')">Область</a>
                                <a href="" ng-class="{'label label-info':editors[item.name]=='editor'}"
                                   ng-click="editorView(item.name,'editor')">Рдактор</a>


                            </div>
                            <div class="span6">
                                <input type="text" ng-if="editors[item.name]=='simple'" ng-disabled="editIsStopped"
                                       ng-model="item.data">
                                <textarea ng-if="editors[item.name]=='textarea'" ng-disabled="editIsStopped" id="{{item.name}}"
                                          cols="30" rows="10" ng-model="item.data"></textarea>

                                <textarea ng-if="editors[item.name]=='editor'" data-ck-editor ng-disabled="editIsStopped"
                                          id="{{item.name}}" cols="30" rows="10" ng-model="item.data"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>







        </div>
        <div ng-show="visible.reviewDiv">

            <div ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/reviewAdmin.html'"></div>

        </div>
    </div>
</div>