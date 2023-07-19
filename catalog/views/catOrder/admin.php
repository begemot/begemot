<?php
/* @var $this CatOrderController */
/* @var $model CatOrder */



$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#cat-order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление заказами</h1>




<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'cat-order-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'phone',
		/*
		'itemIdArray',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
