<?php
$this->breadcrumbs=array(
	'Callbacks'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Callback','url'=>array('index'))
);

?>

<h1>Manage Callbacks</h1>


<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'callback-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'date',
		'group',
		'title',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{view}{delete}'
		),
	),
)); ?>
