<?php

$this->menu = require dirname(__FILE__) . '/../catItem/commonMenu.php';
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js');

Yii::app()->clientScript->registerScriptFile('/protected/modules/contentTask/assets/js/paging.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/assets/js/massOperation.js');
?>

<h1>Массовые операции с позициями</h1>
<div ng-app="massOperation" ng-controller="index">
    <h2>Выбор базового раздела</h2>
    Категория:<select class="span3" ng-model="selectedCat"
                      ng-options="item as item.name for item in catCategories | categories:searchCatStr">

    </select>
    фильтр категорий:<input type="text" ng-model="searchCatStr"> Выбрано:{{countOfCheckedItems()}}
    <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">Меню <span class="caret"></span></button>
        <ul class="dropdown-menu">
            <li><a onClick="$('#categories').modal('toggle')" ng-click="catMsgShow=false">Перемещение/Копирование</a></li>

        </ul>
    </div>
    <table class="items table table-striped table-bordered table-condensed">
        <thead>
        <tr>
            <th class="image-column">&nbsp;</th>
            <th><input onclick="setCheckboxCall();" class="gridCheckboxCheckAll" type="checkbox"></th>
            <th id="test-grid_c2" style="width: 60px;">
                <a class="sort-link" ng-click="idAsc=(idAsc+1) % 2">ID
                    <span ng-show="idAsc==0">(убыв.)</span>
                    <span ng-show="idAsc==1">(возр.)</span>
                </a></th>
            <th id="test-grid_c3"><a class="sort-link"
                                     href="/catalog/catItem/index/CatItem_sort/article.htm">Article<span
                            class="caret"></span></a></th>

            <th id="test-grid_c5"><a class="sort-link"
                                     href="/catalog/catItem/index/CatItem_sort/name.htm">Наименование<span
                            class="caret"></span></a></th>
            <th >Цена</th>
            <th >Переключатель публикации</th>


        </tr>
        <tr class="filters">
            <td>&nbsp;</td>
            <td>
                <div class="filter-container">&nbsp;</div>
            </td>
            <td>
                <div class="filter-container"><input name="CatItem[id]" id="CatItem_id" type="text" style="width: 60px;"></div>
            </td>
            <td>
                <div class="filter-container"><input name="CatItem[article]" id="CatItem_article" type="text"
                                                     maxlength="255"></div>
            </td>
            <td>
                <div class="filter-container">&nbsp;</div>
            </td>
            <td>
                <div class="filter-container"><input name="CatItem[name]" id="CatItem_name" type="text" maxlength="255">
                </div>
            </td>
            <td></td>
            <td>
                <div class="filter-container">&nbsp;</div>
            </td>


        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="(index,item) in catItems">
            <td width="120"><img alt="no" width="120" height="120" src="{{item.img}}">
            </td>
            <td><input class="gridCheckbox" type="checkbox" ng-checked="isMarked(item.id)"
                       ng-click="checkClick(item.id)"></td>
            <td>{{item.id}}</td>
            <td>&nbsp;</td>

            <td>{{item.name}}</td>
            <td><input type="text" ng-model="item.price" ng-change="saveField('price',item.price,item.id)"></td>
            <td><input type="checkbox" class="togglePublished" data-id="1497">&nbsp;</td>


        </tr>

        </tbody>
    </table>
    <div class="pagination">
        <div paging
             page="page"
             page-size="20"
             total="count"
             paging-action="setPage( page)">
        </div>

    </div>

    <div class="modal" id="categories" style="display: none;width:70%;margin-left: -35%;height:500px">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Работа с разделами выбранных позиций</h3>
        </div>
        <div class="modal-body" style="max-height: 361px;">
            <h3>Выбор базового раздела</h3>
            <div class="alert alert-block" ng-show="catMsgShow">
                {{catMsg}}
            </div>
            <p>Перемещение работает на раздел. Т.к. выбранные позиции могут находиться сразу в нескольких разделах,
                то перемещение сработает только при выбранном исходящим разделе и переместятся только те позиции,
                которые в этом разделе есть.

            </p>
            Исходная категория:<select class="span3" ng-model="selectedCat"
                                       ng-options="item as item.name for item in catCategories | categories:searchCatStr">

            </select>
            фильтр категорий:<input type="text" ng-model="searchCatStr"> Выбрано:{{countOfCheckedItems()}}
            <br><br>
            Категория целевая:<select class="span3" ng-model="targetSelectedCat"
                                      ng-options="item as item.name for item in targetCatCategories | categories:targetSearchCatStr">

            </select>
            фильтр категорий:<input type="text" ng-model="targetSearchCatStr">
        </div>
        <div class="modal-footer">

            <a class="btn btn-primary" ng-click="moveToCategory()">Переместить</a>
        </div>
    </div>

</div>



