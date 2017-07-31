<?php
/* @var $this PriceCatsController */
/* @var $model PriceCats */

$this->breadcrumbs=array(
	'Price Cats'=>array('index'),
	$model->name,
);

$this->menu=require (dirname(__FILE__).'/../commonMenu.php');
?>

<h1>View PriceCats #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'order',
	),
)); ?>
