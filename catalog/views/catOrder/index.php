<?php
/* @var $this CatOrderController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Cat Orders',
);

$this->menu=array(
	array('label'=>'Create CatOrder', 'url'=>array('create')),
	array('label'=>'Manage CatOrder', 'url'=>array('admin')),
);
?>

<h1>Cat Orders</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
