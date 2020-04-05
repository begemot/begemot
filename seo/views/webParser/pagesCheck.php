<?php

Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.js');
Yii::app()->clientScript->registerScriptFile("/protected/modules/contentTask/assets/js/paging.js");
$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
?>


<script>
    var app = angular.module('pagesCheck', ['bw.paging']);
    app.controller('page-controller', ['$scope', '$compile', '$http', function ($scope, $compile, $http) {

        $scope.page = 0
        $scope.totalCount = 0
        $scope.tableData = []

        $scope.searchId = '';
        $scope.searchUrl = '';
        $scope.searchUid = '';

        $scope.sortField = 'id';
        $scope.sortMethod = 'asc';


        $scope.loadData = function () {
            $http.get('/seo/webParser/pagesCheckData',
                {
                    params:
                        {
                            page: $scope.page,
                            id:$scope.searchId,
                            url:$scope.searchUrl,
                            uid:$scope.searchUid,
                            sort:$scope.sortField,
                            direction:$scope.sortMethod
                        }
                }).then(function (response) {
                console.log(response.data)
                $scope.tableData = response.data.data
                $scope.totalCount = response.data.totalCount
            });
        }

        $scope.loadData();

        $scope.sendCheckRequest = function (pageId) {
            $http.get('/seo/webParser/sendCheckRequest',{params:{pageId:pageId}}).then(function(){
                $scope.loadData();
            })
        };

        $scope.setSort = function (filterName){
            console.log('Сортируем')
            if ($scope.sortField != filterName){
                $scope.sortField = filterName
                $scope.sortMethod = 'desc'
            }else {
                if($scope.sortMethod == 'desc'){

                    $scope.sortMethod = 'asc'
                }else {
                    $scope.sortMethod = 'desc'
                }
            }



            $scope.loadData()
        }

        $scope.setPage = function (page) {
            $scope.page = page
            $scope.loadData()
        }

    }
    ])

</script>

<div ng-app="pagesCheck" ng-controller="page-controller">


    <div id="seo-pages-grid" class="grid-view">
        <div class="pagination" paging
             page="page"
             page-size="20"
             total="totalCount"
             paging-action="setPage(page)">
        </div>
        <div class="summary">Элементы {{(page-1)*20 +1}} - {{(page*20)}} из {{totalCount}}.</div>
        <table class="items table">
            <thead>

                <th width="50" ng-click="setSort('id')"><a ng-class="{desc:sortField=='id',asc:sortField!='id'}" class="sort-link asc" href="">Id<span class="caret"></span></a></th>
                <th ng-click="setSort('url')"><a ng-class="{desc:sortField=='url',asc:sortField!='url'}"  class="sort-link asc" href="">Url<span class="caret"></span></a></th>
                <th ng-click="setSort('uid')"><a ng-class="{desc:sortField=='uid',asc:sortField!='uid'}"  class="sort-link asc" href="">Uid<span class="caret"></span></a></th>
                <th ng-click="setSort('text_unique')" width="100"><a ng-class="{desc:sortField=='text_unique',asc:sortField!='text_unique'}"  class="sort-link asc" href="">Уник.<span class="caret"></span></a></th>
                <th width="100">&nbsp;</th>
            </tr>
            <tr class="filters">
                <td>
                    <div class="filter-container"><input ng-model='searchId' ng-change="loadData()" type="text">
                    </div>
                </td>
                <td>
                    <div class="filter-container"><input ng-model='searchUrl' ng-change="loadData()" type="text">
                    </div>
                </td>
                <td>
                    <div class="filter-container"><input ng-model='searchUid' ng-change="loadData()" type="text">
                    </div>
                </td>
                <td>

                </td>
                <td>
                    <div class="filter-container">&nbsp;</div>
                </td>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="line in tableData">
                <td>{{line.id}}</td>
                <td><a href="{{line.url}}">{{line.url}}</a></td>
                <td>{{line.uid}}</td>
                <td>
                    {{line.checkError}}
                    {{line.text_unique}}
                </td>
                <td>
                    <button class="btn btn-small" ng-click="sendCheckRequest(line.id)">Запрос на проверку</button>
                </td>
            </tr>


            </tbody>
        </table>
        <div class="pagination" paging
             page="page"
             page-size="20"
             total="totalCount"
             paging-action="setPage(page)">
        </div>
    </div>
</div>
