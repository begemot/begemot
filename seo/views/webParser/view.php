<?php
/* @var $this WebParserController */
/* @var $model WebParser */

$this->breadcrumbs=array(
	'Web Parsers'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List WebParser', 'url'=>array('index')),
	array('label'=>'Create WebParser', 'url'=>array('create')),
	array('label'=>'Update WebParser', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete WebParser', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage WebParser', 'url'=>array('admin')),
);
?>

<h1>View WebParser #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'date',
		'report_text',
		'processTime',
		'pagesProcessed',
		'status',
	),
)); ?>
