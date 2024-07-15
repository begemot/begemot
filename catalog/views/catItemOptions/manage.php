<?php
// Подключение jQuery и Lodash из bower_components

// Получаем объект clientScript
$cs = Yii::app()->clientScript;

// Подключение jQuery и Lodash из bower_components
$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/jquery/dist/jquery.min.js', 1);
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
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/views/catItemOptions/js/manage.angular.js', CClientScript::POS_END);

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

                <cat-item-select menu-mode='true' selected-items="selectedItems" on-select-change="onSelectAndUnselect(items)" selected-items-view='1'>
                    <div>{{item.name}}</div>


                </cat-item-select>
            </div>
      

        </div>
    </div>
    <div class="container mt-5">
        <h1>Пакетное добавление опций</h1>
        
        <button class="btn btn-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#sampleTable" aria-expanded="false" aria-controls="sampleTable">
            Показать/Скрыть образец таблицы
        </button>
        <div class="collapse mt-3" id="sampleTable">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Заголовок 1</th>
                        <th>Заголовок 2</th>
                        <th>Заголовок 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Данные 1</td>
                        <td>Данные 2</td>
                        <td>Данные 3</td>
                    </tr>
                    <tr>
                        <td>Данные 4</td>
                        <td>Данные 5</td>
                        <td>Данные 6</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button class="btn btn-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#mdTableSample" aria-expanded="false" aria-controls="mdTableSample">
            Показать/Скрыть образец таблицы MD
        </button>
        <div class="collapse mt-3" id="mdTableSample">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Заголовок A</th>
                        <th>Заголовок B</th>
                        <th>Заголовок C</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Данные A</td>
                        <td>Данные B</td>
                        <td>Данные C</td>
                    </tr>
                    <tr>
                        <td>Данные D</td>
                        <td>Данные E</td>
                        <td>Данные F</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="mt-4">Текст MD таблицы с данными</h2>
        <textarea class="form-control mt-3" rows="5" placeholder="Введите данные здесь..."></textarea>
        <button class="btn btn-success mt-3">Отправить</button>
    </div>

</div>