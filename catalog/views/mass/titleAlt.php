<?php
// Подключение jQuery и Lodash из bower_components

// Получаем объект clientScript
$cs = Yii::app()->clientScript;

// Подключение jQuery и Lodash из bower_components

$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/lodash/dist/lodash.min.js', CClientScript::POS_BEGIN);

// Подключение скриптов из модуля begemot
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/commonUi.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/modal.commonUi.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/jsonTable.commonUi.directive.js', CClientScript::POS_BEGIN);

// Подключение скриптов из модуля catalog
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/uiModule.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemSelect.directive.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemCatList.directive.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemList.directive.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/categorySelect.directive.js', CClientScript::POS_BEGIN);



// Подключение скрипта massItemsMoveBetweenCategories
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/views/mass/js/titleAlt.angular.js', CClientScript::POS_BEGIN);

?>

<div ng-app="myApp" ng-controller="myCtrl">
    <style>
        .scrollable-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .list-group-item {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .list-group-item:hover {
            background-color: #e0e0e0;
        }

        .selected-item {
            background-color: #a8d5e2;
            color: #ffffff;
        }

        .schema-data-list {
            list-style-type: none;
            padding: 0;
        }

        .schema-data-list li {
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .schema-data-list li:last-child {
            border-bottom: none;
        }
    </style>
    <h1>Массовое изменение title и alt</h1>
    <div class="container-fluid">
        <div class="row">
            <div>
                <cat-item-select menu-mode='true' selected-items="selectedItems" on-select-change="onSelectAndUnselect(items)" selected-items-view='1'>
                    <div>{{item.name}}</div>
                </cat-item-select>
            </div>
            <button type="button" class="btn btn-primary col m-3" ng-click='saveData()'>
                Сохранить данные
            </button>
            <button type="button" class="btn btn-primary col m-3" ng-click='toggleJsonWindow()'>
                Показать json
            </button>
        </div>
    </div>

    <div class="row">
        <div ng-if="data.images">
            <h3>Images from API:</h3>
            <div ng-repeat="(key, image) in data.images" class="row mb-4">
                <div class="col-md-4">
                    <img ng-src="{{image.original}}" alt="{{image.alt}}" class="img-fluid" />
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title_{{key}}" class="form-label">Title:</label>
                        <input type="text" id="title_{{key}}" ng-model="image.title" class="form-control" >
                    </div>
                    <div class="mb-3">
                        <label for="alt_{{key}}" class="form-label">Alt:</label>
                        <input type="text" id="alt_{{key}}" ng-model="image.alt" class="form-control" >
                    </div>
                </div>
            </div>
        </div>

    </div>

    <modal visible='windowVisible'> 
        <textarea id="jsonInput" class="form-control" rows="10" ng-model="jsonTitles.data">

    </textarea><button type="button" class="btn btn-primary" ng-click="applyJsonTitles()">Apply Titles

    </button>
</modal>
</div>