<?php
/* @var $this TasksToUserLikesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Tasks To User Likes',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Tasks To User Likes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
