<?php
/* @var $this ContentTaskController */
/* @var $model ContentTask */

$this->breadcrumbs=array(
	'Content Tasks'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ContentTask', 'url'=>array('index')),
	array('label'=>'Manage ContentTask', 'url'=>array('admin')),
);
?>

<h1>Create ContentTask</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>