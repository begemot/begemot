<?php
/* @var $this LikesAndDislikesController */
/* @var $model TasksLikesAndDislikes */

$this->breadcrumbs=array(
	'Tasks Likes And Dislikes'=>array('index'),
	$model->id,
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>View TasksLikesAndDislikes #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'like_or_dislike',
		'task_id',
		'user_id',
	),
)); ?>
