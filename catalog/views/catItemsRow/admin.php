<?php
/* @var $this CatItemsRowController */
/* @var $model CatItemsRow */

$this->breadcrumbs=array(
	'Cat Items Rows'=>array('index'),
	'Manage',
);

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';



?>

<h1>Управление дополнительными полями элемента каталога</h1>


<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'cat-items-row-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
        'type'=>'striped bordered condensed',
	'columns'=>array(
		'name',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
