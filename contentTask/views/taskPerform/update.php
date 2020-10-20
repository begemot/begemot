<?php
Yii::app()->clientScript->registerScriptFile('https://cdn.ckeditor.com/4.5.6/standard/ckeditor.js');

Yii::app()->clientScript->registerScriptFile('https://cdn.jsdelivr.net/lodash/4.10.0/lodash.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-resource.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-sanitize.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/paging.js');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular-route.js');


Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/perform.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/editViewSchemas.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/editController.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/editDirectives.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/subTaskService.angular.js');


Yii::app()->clientScript->registerCssFile('/protected/modules/contentTask/assets/css/edit.css');
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
                <li ng-class="{active:tabName=='Base'}" ng-show="visible.pageBase">
                    <a href="/contentTask/taskPerform/edit/accessCode/{{accessCode}}/id/{{subtaskId}}/#!/">Исходные</a>
                </li>
                <li ng-class="{active:tabName=='Current'}" ng-click="panel('current')" ng-show="visible.pageCurrent">
                    <a href="/contentTask/taskPerform/edit/accessCode/{{accessCode}}/id/{{subtaskId}}/#!/tab/Current">Текущая
                        работа</a>
                </li>
                <li ng-class="{active:tabName=='ReviewAdmin'}"
                    ng-show="visible.pageReview">
                    <a href="/contentTask/taskPerform/edit/accessCode/{{accessCode}}/id/{{subtaskId}}/#!/tab/ReviewAdmin">Правки</a>
                </li>
                <li ng-class="{active:tabName=='Images'}" ng-show="visible.pageImages">
                    <a href="/contentTask/taskPerform/edit/accessCode/{{accessCode}}/id/{{subtaskId}}/#!/tab/Images">Изображения</a>
                </li>
                <li>
                    <div style="margin: 5px 5px" class="btn-group" ng-show="visible.btnSave">
                        <a class="btn btn-primary btn-small" href="" ng-click="save()"> {{saveText}}</a>
                    </div>
                    <div style="margin: 5px 5px" class="btn-group" ng-show="visible.btnSendForReview">
                        <a class="btn btn-warning btn-small" href="" ng-click="sendToReview()"> {{reviewText}}</a>
                    </div>

                </li>
                <li>

                    <my-customer></my-customer>


                </li>
            </ul>


        </div>

        <div ng-show="visible.pageImages && (!imagesShow)"
             ng-include="'/protected/modules/contentTask/views/taskPerform/jstpl/updateTab'+tabName+'.html'"></div>
        <div ng-show="visible.pageImages && imagesShow">
            <?php
            Yii::import('contentTask.taskTypes.Catalog');
            $picturesConfig = array();
            $configFile = Yii::getPathOfAlias(Catalog::$imageConfigAlias).'.php' ;
//            $configFile = Yii::getPathOfAlias('webroot.protected.config.catalog.categoryItemPictureSettings').'.php' ;
            if (file_exists($configFile)) {

                $picturesConfig = require($configFile);

                $this->widget(
                    'application.modules.pictureBox.components.PictureBox', array(
                        'id' => 'contentManagerSubTask',
                        'elementId' => $_REQUEST['id'],
                        'config' => $picturesConfig,
                        'theme' => 'tiles'
                    )
                );
            } else {
                Yii::app()->user->setFlash('error', 'Отсутствует конфигурационный файл:' . $configFile);
            }

            ?>
        </div>
        <div ng-show="visible.dataDiv">

            <div class="row">
                <div class="hero-unit span8">
                    <h1>Работа на проверке</h1>
                    <p>Это подзадание отправленно на проверку администратором. После проверки задание либо вернется в
                        работу
                        и появится на вкладке "Правки", либо появится во вкладке "Завершено".
                        Что бы ускорить проверку свяжитесь с человеком, который должен принять у вас работу.
                    </p>

                </div>
            </div>

            <!--            <div class="tabbable tabs-left">-->
            <!--                <ul class="nav nav-tabs">-->
            <!--                    <li ng-class="{active:item.name==dataActiveTab}" ng-repeat="(index,item) in visibleData"><a-->
            <!--                                href="#{{item.name}}" data-toggle="tab">{{item.name}}</a></li>-->
            <!---->
            <!--                </ul>-->
            <!--                <div class="tab-content">-->
            <!--                    <div class="tab-pane" ng-class="{active:item.name==dataActiveTab}" id="{{item.name}}"-->
            <!--                         ng-repeat="(index,item) in visibleData">-->
            <!---->
            <!--                        <div>-->
            <!--                            <div class="">-->
            <!--                                <h3>Поле "{{item.name}}"</h3>-->
            <!--                            </div>-->
            <!--                            <div class="span1" style="text-align: right">-->
            <!--                                <a href="" ng-class="{'label label-info':editors[item.name]=='simple'}"-->
            <!--                                   ng-click="editorView(item.name,'simple')">Поле</a> <br>-->
            <!--                                <a href="" ng-class="{'label label-info':editors[item.name]=='textarea'}"-->
            <!--                                   ng-click="editorView(item.name,'textarea')">Область</a>-->
            <!--                                <a href="" ng-class="{'label label-info':editors[item.name]=='editor'}"-->
            <!--                                   ng-click="editorView(item.name,'editor')">Рдактор</a>-->
            <!---->
            <!---->
            <!--                            </div>-->
            <!--                            <div class="span6">-->
            <!--                                <input type="text" ng-if="editors[item.name]=='simple'" ng-disabled="editIsStopped"-->
            <!--                                       ng-model="item.data">-->
            <!--                                <textarea ng-if="editors[item.name]=='textarea'" ng-disabled="editIsStopped"-->
            <!--                                          id="{{item.name}}"-->
            <!--                                          cols="30" rows="10" ng-model="item.data"></textarea>-->
            <!---->
            <!--                                <textarea ng-if="editors[item.name]=='editor'" data-ck-editor-->
            <!--                                          ng-disabled="editIsStopped"-->
            <!--                                          id="{{item.name}}" cols="30" rows="10" ng-model="item.data"></textarea>-->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!---->
            <!--                </div>-->
            <!--            </div>-->


        </div>

    </div>
</div>
