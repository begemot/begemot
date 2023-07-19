<?php
/* @var $this DataController */
/* @var $model WebParser */

$this->breadcrumbs=array(
	'Web Parsers'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List WebParser', 'url'=>array('index')),
	array('label'=>'Create WebParser', 'url'=>array('create')),
	array('label'=>'View WebParser', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage WebParser', 'url'=>array('admin')),
);
?>

<h1>Update WebParser <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>