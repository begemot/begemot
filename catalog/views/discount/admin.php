<?php
/* @var $this PromoController */
/* @var $model Promo */

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';



$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'discount-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
