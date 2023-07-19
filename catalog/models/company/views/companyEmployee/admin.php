<?php
/* @var $this CompanyEmployeeController */
/* @var $model CompanyEmployee */


$this->menu=$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';


?>
<h1>Управление сотрудниками</h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'company-employee-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'position',

		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
		array(
			'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
			'header' => 'порядок',
		),
	),
)); ?>
