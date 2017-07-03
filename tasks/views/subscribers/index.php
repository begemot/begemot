<?php
/* @var $this SubscribersController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Tasks Subscribes',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Tasks Subscribes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
