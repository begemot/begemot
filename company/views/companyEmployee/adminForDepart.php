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
		array(
			'header' => 'Top',
			'type' => 'raw',
			'value'=>'$data->emp->name',
		),


		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
