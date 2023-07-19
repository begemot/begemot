<?php
/* @var $this PromoController */
/* @var $model Promo */

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';



 $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'promo-grid',
	'dataProvider'=>$model->search(),//$model->search($id),
	'filter'=>$model,
        'type'=>'striped bordered condensed',
	'columns'=>array(
		'id',
		'title',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
                        'updateButtonUrl'=>'"/catalog/promo/update/id/".$data->id',
		),
        array(
                'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
                "header"=>"порядок",
        ),
	),
));?>
