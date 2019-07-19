<?php
/* @var $this ContentTaskController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Content Tasks',
);

$this->menu=array(
	array('label'=>'Create ContentTask', 'url'=>array('create')),
	array('label'=>'Manage ContentTask', 'url'=>array('admin')),
);
?>

<h1>Content Tasks</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
