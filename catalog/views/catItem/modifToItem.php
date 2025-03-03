<?php
$this->renderPartial('bs5TabMenu', ['model' => $model, 'tab' => $tab]);
?>
<h4>Модификации catItem</h4>

<?php
// Подключение jQuery и Lodash из bower_components

// Получаем объект clientScript
$cs = Yii::app()->clientScript;

// Подключение jQuery и Lodash из bower_components

$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/lodash/dist/lodash.min.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile(Yii::app()->baseUrl . '/bower_components/jquery/dist/jquery.min.js', CClientScript::POS_BEGIN);

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
$cs->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/catalog/views/catItem/js/manage-modif.angular.js', CClientScript::POS_BEGIN);



?>

<div ng-app="myApp" ng-controller="myCtrl" ng-init="init(<?= $model->id ?>)">
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

        <cat-item-select hide-id='<?= $model->id ?>' selected-items="selectedItems"
            on-select-change="onSelectAndUnselect(items)" selected-list-title='Модификации товара'>
        </cat-item-select>

    </div>

</div>