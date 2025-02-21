<?php
$assetsUrl = Yii::app()->getModule('videoGallery')->getAssetsUrl();
Yii::app()->clientScript->registerScriptFile($assetsUrl . '/js/videoRelationWidget.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile($assetsUrl . '/css/videoRelationWidget.css');
?>


<div ng-app="videoApp">
    <div ng-controller="VideoRelationController">
        <video-relation-widget entity-type="<?= $entityType ?>" entity-id="<?= $entityId ?>"></video-relation-widget>
    </div>
</div>