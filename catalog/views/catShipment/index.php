<?php
/* @var $this CatShipmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Cat Shipments',
);

$this->menu=array(
	array('label'=>'Create CatShipment', 'url'=>array('create')),
	array('label'=>'Manage CatShipment', 'url'=>array('admin')),
);
?>

<h1>Cat Shipments</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
