<?php
/* @var $this LikesAndDislikesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Tasks Likes And Dislikes',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Tasks Likes And Dislikes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
