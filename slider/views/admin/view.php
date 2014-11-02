<?php
/* @var $this SliderController */
/* @var $model Slider */

$this->breadcrumbs=array(
	'Sliders'=>array('index'),
	$model->id,
);

require Yii::getPathOfAlias('webroot').'/protected/modules/slider/views/admin/_menu.php';
?>

<h1><? echo Yii::t('SliderModule.msg','View Slider'); ?> #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
      array('name'=>'image', 'value'=>CHtml::image($model->image), 'type'=>'raw'),
		'header',
		'text1',
		'text2',
		'text3',
	),
)); ?>
