<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <base href="<?=$_SERVER['HTTP_HOST']?>"/>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<?php Yii::app()->clientScript->registerCssFile('/bower_components/angular-material/angular-material.css');?>

<?php Yii::app()->clientScript->registerScriptFile('/bower_components/angular/angular.js');?>
<?php Yii::app()->clientScript->registerScriptFile('/bower_components/angular-aria/angular-aria.js');?>
<?php Yii::app()->clientScript->registerScriptFile('/bower_components/angular-animate/angular-animate.js');?>
<?php Yii::app()->clientScript->registerScriptFile('/bower_components/angular-messages/angular-messages.js');?>
<?php Yii::app()->clientScript->registerScriptFile('/bower_components/angular-material/angular-material.js');?>


<?php

//$path = Yii::getPathOfAlias('begemot.views.default');
//$this->renderFile($path.'/bs5topMenu.php',['menu'=>['123123123']]);
////$this->renderPartial('begemot/default/bs5topMenu');
//?>


<div class="container">
    <?php echo $content; ?>
</div>

</body>