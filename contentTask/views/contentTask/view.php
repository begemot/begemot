<?php
/* @var $this ContentTaskController */
/* @var $model ContentTask */

$this->breadcrumbs=array(
	'Content Tasks'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List ContentTask', 'url'=>array('index')),
	array('label'=>'Create ContentTask', 'url'=>array('create')),
	array('label'=>'Update ContentTask', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ContentTask', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ContentTask', 'url'=>array('admin')),
);
?>

<h1>View ContentTask #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'text',
		'type',
		'actionsList',
		'dataElementsList',
	),
)); ?>
