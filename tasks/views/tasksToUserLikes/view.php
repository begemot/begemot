<?php
/* @var $this TasksToUserLikesController */
/* @var $model TasksToUserLikes */

$this->breadcrumbs=array(
	'Tasks To User Likes'=>array('index'),
	$model->id,
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>View TasksToUserLikes #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'tasks_to_user_id',
		'user_id',
		'id',
	),
)); ?>
