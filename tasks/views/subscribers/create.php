<?php
/* @var $this SubscribersController */
/* @var $model TasksSubscribe */

$this->breadcrumbs=array(
	'Tasks Subscribes'=>array('index'),
	'Create',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Create TasksSubscribe</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>