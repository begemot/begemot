<?php
/* @var $this CatShipmentController */
/* @var $model CatShipment */

$this->breadcrumbs=array(
	'Cat Shipments'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List CatShipment', 'url'=>array('index')),
	array('label'=>'Create CatShipment', 'url'=>array('create')),
	array('label'=>'Update CatShipment', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete CatShipment', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage CatShipment', 'url'=>array('admin')),
);
?>

<h1>View CatShipment #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'price',
	),
)); ?>
