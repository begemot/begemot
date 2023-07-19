<?php
/* @var $this CompanyEmployeeController */
/* @var $model CompanyEmployee */



$this->menu=$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<h1>View CompanyEmployee #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'position',
		'text',
	),
)); ?>
