<?php
Yii::app()->clientScript->registerScriptFile("https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js");

Yii::app()->clientScript->registerScriptFile("/protected/modules/seo/assets/js/tagsProcess.js");
Yii::app()->clientScript->registerScriptFile("/protected/modules/seo/assets/js/tagsTable.js");
Yii::app()->clientScript->registerScriptFile("/protected/modules/contentTask/assets/js/paging.js");

$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
?>
<style>
    .wrong {
        color:black;
        background-color: red;
    }
</style>
<h1>Анализ html-тегов сайта</h1>
<p>Делается после парсинга всех страниц сайта.</p>

<div ng-app="app" ng-controller="tagsProcess">
    <div>Процесс:{{executed}}/{{count}}</div>

    <div ng-controller="tagsTable">
        <div class="pagination">
            <div paging
                 page="activePage"
                 page-size="10"
                 total="dataCount"
                 paging-action="setPage(page)">
            </div>

        </div>
        <table >
            <tr>
                <td></td> <td></td>
                <td ng-repeat="(key,value) in cols" ng-if="value.enabled ==true"><a href="" ng-click="setSort(key)">{{key}}</a></td>
            </tr>
            <tr ng-repeat="data in tagsData">
                <td><a href="{{data['url']}}" target="_blank">Перейти</a></td>
                <td><a href="" ng-click="computePageTags(data['pageId'])" target="_blank">Обновить</a></td>
                <td ng-repeat="(key,value) in cols" ng-if="value.enabled ==true" class="{{checkTag(key,data[key])}}">
                    <span ng-if="key!='url'">
                        {{data[key]}}
                    </span>
                </td>

            </tr>
        </table>
    </div>

</div>

<?php


?>
