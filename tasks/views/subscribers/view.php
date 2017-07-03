<?php
/* @var $this SubscribersController */
/* @var $model TasksSubscribe */

$this->breadcrumbs=array(
	'Tasks Subscribes'=>array('index'),
	$model->id,
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>View TasksSubscribe #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'task_id',
		'email',
		'id',
	),
)); ?>
