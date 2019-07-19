<?php

require_once (__DIR__.'/common.php');


$this->breadcrumbs = array(
    $this->module->id,
);
?>
    <h1>Управление заданием</h1>
    <ul class="nav nav-tabs">
        <li>
            <a href="/contentTask/contentTask/update?id=<?= $_REQUEST['id'] ?>">Основные</a>
        </li>
        <li class="active">
            <a href="/contentTask/contentTask/search?id=<?= $_REQUEST['id'] ?>">Поиск</a>
        </li>
        <li>
            <a href="/contentTask/contentTask/added?id=<?= $_REQUEST['id'] ?>">Добавленные</a>
        </li>
    </ul>


    <div ng-app="app">
        <div ng-controller="search">
            <div class="pagination">
                <div paging
                     page="activePage"
                     page-size="20"
                     total="recordsCount"
                     paging-action="setPage( page)">
                </div>

            </div>

            <table class="table table-bordered">
                <tr>
                    <td>id</td>
                    <td>Название</td>
                    <td></td>
                </tr>
                <tr>
                    <td><input type="text" class="span2 search-query" style="width: 200px;" ng-model="searchId"></td>
                    <td> <input type="text" class="span2 search-query" style="width: 200px;" ng-model="searchTitle"></td>
                    <td><button type="submit" class="btn" ng-click="makeSearch()">Search</button></td>
                </tr>
                <tr ng-repeat="(index,item) in resultItems">
                    <td>{{item.id}}</td>
                    <td>{{item.title}}</td>
                    <td><span style="color: green;" ng-show="item.added!==null" >Добавлено</span><button ng-show="item.added==null" type="submit" class="btn" ng-click="addToTask(index)">Добавить</button></td>
                </tr>
            </table>
            <div class="pagination">
                <div paging
                     page="activePage"
                     page-size="20"
                     total="recordsCount"
                     paging-action="setPage( page)">
                </div>

            </div>
        </div>
    </div>

<?php


?>