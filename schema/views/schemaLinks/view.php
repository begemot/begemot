<?php
/* @var $this SchemaLinksController */
/* @var $model SchemaLinks */

$this->breadcrumbs=array(
	'Schema Links'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List SchemaLinks', 'url'=>array('index')),
	array('label'=>'Create SchemaLinks', 'url'=>array('create')),
	array('label'=>'Update SchemaLinks', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete SchemaLinks', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage SchemaLinks', 'url'=>array('admin')),
);
?>

<h1>View SchemaLinks #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'linkType',
		'linkId',
		'schemaId',
	),
)); ?>
