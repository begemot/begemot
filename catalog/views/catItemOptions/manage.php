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
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/views/catItemOptions/js/manage.angular.js', CClientScript::POS_BEGIN);

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

    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <cat-item-select menu-mode='true' selected-items="selectedItems" on-select-change="onSelectAndUnselect(items)" selected-items-view='1'>
                    <div>{{item.name}}</div>
                </cat-item-select>
            </div>
            <div class="col-6">
                <h1 class="my-4">Options List</h1>
                <div class="list-group" style="max-height: 400px; overflow-y: auto;">
                    <a href="#" class="list-group-item list-group-item-action" ng-repeat="option in optionsListOfSelected">
                        <div class="d-flex w-100 justify-content-between">
                            <img ng-src="{{ option.image }}" class="img-thumbnail me-3" alt="{{ option.name }}" style="width: 50px; height: 50px;">
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ option.name }}</h5>
                                <p class="mb-1">Price: {{ option.price | currency }}</p>
                                <small>Is Base: {{ option.isBase == '1' ? 'Yes' : 'No' }}</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <div class="container mt-5">
        <div class="row mt-5">
            <h2>Импорт данных JSON</h2>
            <json-table input-data="inputData" output-data="outputData" send-data-url='/catalog/api/massOptionsImport' additional-data-for-send='selectedItem'></json-table>

            <p>Если в данных присутствует article или itemId, то будет искать существующие опции и крепить уже существующие. В таком случае игнорируются все данные, кроме 
                информации о привязке. Цены, изображения и остальное не обновиться. На будущее сделать, что бы обновлялись. 
                 Если просто массово добавить, то по образцу.</p>
        </div>
    </div>

</div>
<?php
// // Подключение jQuery и Lodash из bower_components

// // Получаем объект clientScript
// $cs = Yii::app()->clientScript;

// // Подключение jQuery и Lodash из bower_components
// $cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/jquery/dist/jquery.min.js', 1);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/lodash/dist/lodash.min.js', CClientScript::POS_END);

// // Подключение скриптов из модуля catalog
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/uiModule.js', CClientScript::POS_END);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemSelect.directive.js', CClientScript::POS_END);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemCatList.directive.js', CClientScript::POS_END);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemList.directive.js', CClientScript::POS_END);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/categorySelect.directive.js', CClientScript::POS_END);

// // Подключение скриптов из модуля begemot
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/commonUi.js', CClientScript::POS_END);
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/modal.commonUi.js', CClientScript::POS_END);

// // Подключение скрипта massItemsMoveBetweenCategories
// $cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/views/catItemOptions/js/manage.angular.js', CClientScript::POS_END);

?>