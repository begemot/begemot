<?php
Yii::app()->clientScript->registerCssFile('/protected/modules/catalog/assets/css/multi-select.css');
Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.7.5/angular.min.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/catalog/assets/js/options.angular.js');
?>
<div ng-app="optionsImport" ng-controller="index">
    <script>
        itemId = <?=$_REQUEST['id']?>;
        app.value('itemId', itemId);
    </script>


    <div class="ms-container" id="ms-custom-headers">
        <h4>Выберите позицию или раздел из которой нужно импортировать опции</h4>
        <div class="ms-selectable">
            <input type="text" class="search-input"
                   placeholder="Поиск по опциям..." ng-focus="panelView = true" ng-blur="hideItemsPanel('items')"
                   ng-model="catItemSearchStr"
                   ng-change="loadItems()"
            >
            <ul class="ms-list dropdownPart" tabindex="-1" ng-show="panelView" style="position: absolute;z-index: 100;background-color: white">
                <li class="ms-elem-selectable" ng-repeat="item  in catItems "
                    ng-click="selectItem(item)">
                    <span>{{item.name}}
                    </span></li>
            </ul>
        </div>
        <div class="ms-selectable">
            <input type="text" class="search-input"
                   placeholder="Поиск раздела..." ng-focus="catPanelView = true" ng-blur="hideCatsPanel('cats')"
                   ng-model="catCategorySearchStr"
                   ng-change="loadCategories()"
            >
            <ul class="ms-list dropdownPart" tabindex="-1" ng-show="catPanelView" style="position: absolute;z-index: 100;background-color: white">
                <li class="ms-elem-selectable" ng-repeat="item  in catCategories "
                    ng-click="selectCategory(item)">
                    <span>{{item.name}}
                    </span></li>
            </ul>
        </div>
    </div>

    <table class="table" ng-show="(selectedItem !=null) || (selectedCategory !=null) ">
        <thead>
        <tr>
            <th> <span ng-show="selectedItem !=null">Выбранная позиция</span><span ng-show="selectedCategory !=null">Выбранная категория</span></th>
            <th></th>

        </tr>
        </thead>

        <tbody >
        <tr >
            <td>
               <span ng-show="selectedItem !=null"><img width="50" src="{{selectedItem.img}}"></span>
                <span ng-show="selectedCategory !=null"><img width="50" src="{{selectedCategory.img}}"></span>
            </td>
            <td>
                <span ng-show="selectedItem !=null">{{selectedItem.name}}</span>
                <span ng-show="selectedCategory !=null">{{selectedCategory.name}}</span>
            </td>

        </tr>
        <tr  >
            <td><h4>
                    Опции
                    <a class="btn btn-success .btn-mini btn-add-option-relation" ng-click="makeImport()">ИМПОРТИРОВАТЬ</a>
                    <a class="btn btn-success .btn-mini btn-add-option-relation" ng-click="removeOptions()">УБРАТЬ</a>
                </h4></td>
            <td></td>

        </tr>
        <tr ng-repeat="option  in options ">
            <td><img width="50" src="{{option.img}}">
            </td>
            <td>{{option.name}}</td>

        </tr>

        </tbody>
    </table>


</div>


