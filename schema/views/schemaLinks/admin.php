<?php
/* @var $this SchemaLinksController */
/* @var $model SchemaLinks */



$this->menu = require dirname(__FILE__).'/../default/commonMenu.php';

 $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'schema-links-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'linkType',
		'linkId',
		'schemaId',
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
           // 'updateButtonUrl'=>'"/catItem/update/id/".$data->itemId',
        ),
	),
));
