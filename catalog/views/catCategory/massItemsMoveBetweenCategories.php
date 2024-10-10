<?php
// Подключение jQuery и Lodash из bower_components

// Получаем объект clientScript
$cs = Yii::app()->clientScript;

// Подключение jQuery и Lodash из bower_components
$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/jquery/dist/jquery.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/lodash/dist/lodash.min.js', CClientScript::POS_END);

// Подключение скриптов из модуля catalog
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/uiModule.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemSelect.directive.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemCatList.directive.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemList.directive.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/categorySelect.directive.js', CClientScript::POS_END);

// Подключение скриптов из модуля begemot
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/commonUi.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/modal.commonUi.js', CClientScript::POS_END);

// Подключение скрипта massItemsMoveBetweenCategories
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/views/catCategory/js/massItemsMoveBetweenCategories.js', CClientScript::POS_END);

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

    <div class="container mt-5">
        <div class="row mt-5">
            <div>
                <button class="btn btn-primary btn-sm" ng-click="submitData()"
                    ng-if="attachButtonVisible">Прикрепить</button>
                <cat-item-select selected-items="selectedItems" on-select-change="onSelectAndUnselect(items)"
                    selected-items-view='1' show-cats='true' menu-mode='false'>
                    <div>{{item.name}}</div>


                </cat-item-select>
            </div>
            <div class="col-md-6"> </div>

        </div>
    </div>



    <div class="container mt-5">
        <h1>Выбор раздела</h1>

        <div class="mt-3">
            <button class="btn btn-primary" ng-click="moveToCat()">Прикрепить элементы к категориям</button>
        </div>

        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="deleteDirectoriesCheckbox" ng-model='deleteAllCats'>
            <label  class="form-check-label" for="deleteDirectoriesCheckbox">
                Удалить все директории, если есть
            </label>
        </div>
    </div>


    <div class="container mt-5">
        <category-select selected-categories='selectedCategories' business-logic-enabled='true'></category-select>
    </div>





    <script>
        $(document).ready(function() {
            $('input[name="status"]').change(function() {
                var selectedStatus = $(this).next('label').text();
                $('#selected_status').text(selectedStatus);
            });
        });
    </script>

</div>