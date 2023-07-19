<?php
/* @var $this CatOrderController */
/* @var $model CatOrder */

$this->breadcrumbs=array(
	'Cat Orders'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List CatOrder', 'url'=>array('index')),
	array('label'=>'Create CatOrder', 'url'=>array('create')),
	array('label'=>'Update CatOrder', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete CatOrder', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage CatOrder', 'url'=>array('admin')),
);
?>

<h1>View CatOrder #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'phone',
		'mail',
		'information',
		'shipmentId',
		'itemIdArray',
	),
)); ?>
