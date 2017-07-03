<?php
/* @var $this SubscribersController */
/* @var $model TasksSubscribe */

$this->breadcrumbs=array(
	'Tasks Subscribes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Update TasksSubscribe <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>