<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <base href="https://<?= $_SERVER['HTTP_HOST'] ?>" />

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

    <?php Yii::app()->clientScript->registerScriptFile('/bower_components/angular/angular.min.js'); ?>
    <?php //Yii::app()->clientScript->registerScriptFile('/bower_components/bootstrap/dist/js/bootstrap.min.js');
    ?>
    <?php Yii::app()->clientScript->registerScriptFile('/bower_components/bootstrap/dist/js/bootstrap.bundle.min.js', 2); ?>
    <?php Yii::app()->clientScript->registerCssFile('/bower_components/bootstrap/dist/css/bootstrap.min.css'); ?>

    <?php

    $path = Yii::getPathOfAlias('begemot.views.default');
    $this->renderFile($path . '/bs5topMenu.php', ['menu' => ['123123123']]);
    //$this->renderPartial('begemot/default/bs5topMenu');
    ?>
    <?php
    $path = Yii::getPathOfAlias('begemot.views.default');
    $this->renderFile($path . '/leftMenu.php');
    ?>


    <?php echo $content; ?>


</html>