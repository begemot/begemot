<?php
/* @var $this TasksToUserLikesController */
/* @var $model TasksToUserLikes */

$this->breadcrumbs=array(
	'Tasks To User Likes'=>array('index'),
	'Create',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Create TasksToUserLikes</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>