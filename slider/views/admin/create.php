<?php
/* @var $this SliderController */
/* @var $model Slider */

$this->breadcrumbs=array(
	'Sliders'=>array('index'),
	'Create',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/slider/views/admin/_menu.php';
?>

<h1><? echo Yii::t('SliderModule.msg','Create Slider'); ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>