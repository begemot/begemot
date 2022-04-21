<?php
/* @var $this SchemaLinksController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Schema Links',
);

$this->menu=array(
	array('label'=>'Create SchemaLinks', 'url'=>array('create')),
	array('label'=>'Manage SchemaLinks', 'url'=>array('admin')),
);
?>

<h1>Schema Links</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
