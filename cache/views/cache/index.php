<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerCssFile('/bower_components/bootstrap/dist/css/bootstrap.min.css');
$cs->registerScriptFile('/bower_components/jquery/dist/jquery.min.js');
$cs->registerScriptFile('/bower_components/bootstrap/dist/js/bootstrap.bundle.js');
$cs->registerScriptFile('/bower_components/angular/angular.min.js');
$cs->registerScriptFile('/protected/modules/cache/views/cache/app.js');

?>
<style>
    .table-bordered thead th{
        cursor: pointer;
    }
    .table-bordered thead th:hover{
        color:#A91317;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
<div ng-app="cacheApp" ng-controller="cacheCtrl">

    <div class="row">
        <div class="col-auto">
            <dropdown options="options" selected-option="selectedOption" label="Выбрать группу"></dropdown>
        </div>

        <div class="col-auto">
            <ajax-button ng-show="selectedOption!=null" title="Сбросить кеш группу" url="/cache/cache/resetCacheForGroup" method="POST" data="{group:selectedOption}" success="loadData()" error="1"></ajax-button>
        </div>
        <div class="col-auto">
            <ajax-button title="Сбросить весь кеш" url="/cache/cache/resetAllCache" method="POST" data="{}" success="loadData()" error="1"></ajax-button>
        </div>
    </div>

    <table class="table table-dark table-bordered">
        <thead>
        <tr>
            <th ng-click="sortColumn('id')">ID <span class="fa" ng-show="sortBy=='id'" ng-class="{'fa-chevron-up':sortReverse,'fa-chevron-down':!sortReverse}"></span>
            </th>
            <th ng-click="sortColumn('cache_group')">Cache Group <span class="fa" ng-show="sortBy=='cache_group'"
                                                                       ng-class="{'fa-chevron-up':sortReverse,'fa-chevron-down':!sortReverse}" ></span></th>
            <th ng-click="sortColumn('cache_key')">Cache Key <span class="fa" ng-show="sortBy=='cache_key'"
                                                                   ng-class="{'fa-chevron-up':sortReverse,'fa-chevron-down':!sortReverse}"></span></th>
            <th>Value</th>
            <th></th>
        </tr>
        <tr>
            <th><input ng-model="search.id" ng-change="loadData()"></th>
            <th><input ng-model="search.cache_group" ng-change="loadData()"></th>
            <th><input ng-model="search.cache_key" ng-change="loadData()"></th>
            <th><input ng-model="search.value" ng-change="loadData()"></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="cache in caches ">
            <td>{{ cache.id }}</td>
            <td>{{ cache.cache_group }}</td>
            <td>{{ cache.cache_key| limitTo: 20 }}</td>
            <td>{{ cache.value| limitTo: 100}}</td>
            <td>
                <ajax-button
                        title="сбросить"
                        url="/cache/cache/resetCacheForKey"
                        method="POST"
                        data="{group:cache.cache_group,key:cache.cache_key}"
                        success="loadData()"
                        error="1"></ajax-button>

            </td>
        </tr>
        </tbody>
    </table>

    <smart-pagination total-pages="totalPages" current-page="currentPage" max-pages="perPage"
                      set-current-page='setCurrentPage'></smart-pagination>
</div>

