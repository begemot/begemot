<?php
/* @var $this LikesAndDislikesController */
/* @var $model TasksLikesAndDislikes */

$this->breadcrumbs=array(
	'Tasks Likes And Dislikes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Update TasksLikesAndDislikes <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>