<?php
/* @var $this CompanyDepartController */
/* @var $model CompanyDepart */



$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';


?>

<h1>Управление структурными подразделениями компании</h1>


<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'company-depart-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',

		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
		array(
			'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
			'header' => 'порядок',
		),
	),
)); ?>
