<?php
/* @var $this DataController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Web Parsers',
);

$this->menu=array(
	array('label'=>'Create WebParser', 'url'=>array('create')),
	array('label'=>'Manage WebParser', 'url'=>array('admin')),
);
?>

<h1>Web Parsers</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
