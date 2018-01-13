<?php
/* @var $this SliderController */
/* @var $model Slider */

$this->breadcrumbs=array(
	'Sliders'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/slider/views/admin/_menu.php';
?>

<h1>Редактирование слайда <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>