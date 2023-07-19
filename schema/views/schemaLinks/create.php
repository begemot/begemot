<?php
/* @var $this SchemaLinksController */
/* @var $model SchemaLinks */

$this->breadcrumbs=array(
	'Schema Links'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List SchemaLinks', 'url'=>array('index')),
	array('label'=>'Manage SchemaLinks', 'url'=>array('admin')),
);
?>

<h1>Create SchemaLinks</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>