<?php
/* @var $this CatItemController */
/* @var $model CatItem */

$this->breadcrumbs=array(
	'Cat Items'=>array('index'),
	'Manage',
);

$this->menu = require dirname(__FILE__).'/commonMenu.php';

?>

<h1>Все позиции каталога</h1>


<?php

 $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'test-grid',
	'dataProvider'=>$dataProvider,//$model->search($id),
	'filter'=>$model,
        'type'=>'striped bordered condensed',
	'columns'=>array(
		'itemId',
		
		'catId',    
                array(
                      'name'=>'name',
                      'value'=>'$data->item->name',
                    
                  ),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
                        'updateButtonUrl'=>'"/catItem/update/id/".$data->itemId',
		),
                array(
                        'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
                        "header"=>"порядок",
                ),
	),
));



?>
