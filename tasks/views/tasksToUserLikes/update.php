<?php
/* @var $this TasksToUserLikesController */
/* @var $model TasksToUserLikes */

$this->breadcrumbs=array(
	'Tasks To User Likes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Update TasksToUserLikes <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>