<?php
/* @var $this PricesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Prices',
);

$this->menu=array(
	array('label'=>'Create Prices', 'url'=>array('create')),
	array('label'=>'Manage Prices', 'url'=>array('admin')),
);
?>

<h1>Prices</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
