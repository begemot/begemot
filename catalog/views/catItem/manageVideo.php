<?php
// Подключение jQuery и Lodash из bower_components

// Получаем объект clientScript
$cs = Yii::app()->clientScript;

// Подключение jQuery и Lodash из bower_components

$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/lodash/dist/lodash.min.js', CClientScript::POS_BEGIN);

// Подключение скриптов из модуля begemot
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/commonUi.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/begemot/ui/commonUiBs5/modal.commonUi.js', CClientScript::POS_BEGIN);


// Подключение скриптов из модуля catalog
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/uiModule.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemSelect.directive.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemCatList.directive.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/catItemList.directive.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/assets/js/ui/categorySelect.directive.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/videoGallery/assets/js/video-gallery.js', CClientScript::POS_BEGIN);


// Подключение скрипта massItemsMoveBetweenCategories
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/views/catItem/js/manage-video.angular.js', CClientScript::POS_BEGIN);



?>

<div ng-app="myApp" ng-controller="myCtrl">
    <h3>Видео для карточек товара</h3>
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
            <?php if (!isset($model)): ?>
                <div class='col-4'>
                    <cat-item-select menu-mode='true' selected-items="selectedItems"
                        on-select-change="onSelectAndUnselect(items)" selected-items-view='1'>
                        <div>{{item.name}}</div>
                    </cat-item-select>
                </div>
                <div class='col'>
                    Выбран: {{selectedItem.name}}
                    <video-gallery model-name='CatItem' model-id='{{selectedItem.id}}'></video-gallery>
                </div>
            <?php else: ?>
                <div class='col'>
                 
                    <video-gallery model-name='CatItem' model-id='<?= $model->id ?>'></video-gallery>
                </div>
            <?php endif; ?>
        </div>
    </div>



</div>