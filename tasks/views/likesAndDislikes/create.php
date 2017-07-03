<?php
/* @var $this LikesAndDislikesController */
/* @var $model TasksLikesAndDislikes */

$this->breadcrumbs=array(
	'Tasks Likes And Dislikes'=>array('index'),
	'Create',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Create TasksLikesAndDislikes</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>