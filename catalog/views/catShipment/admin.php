<?php
/* @var $this CatShipmentController */
/* @var $model CatShipment */


$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';


?>

<h1>Управление способами доставки</h1>



<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'cat-shipment-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'price',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
