<?php
/* @var $this PricesController */
/* @var $model Prices */

$this->breadcrumbs=array(
	'Prices'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Prices', 'url'=>array('index')),
	array('label'=>'Create Prices', 'url'=>array('create')),
	array('label'=>'Update Prices', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Prices', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Prices', 'url'=>array('admin')),
);
?>

<h1>View Prices #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'catId',
		'price',
		'type',
		'order',
	),
)); ?>
