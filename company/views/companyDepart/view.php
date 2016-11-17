<?php
/* @var $this CompanyDepartController */
/* @var $model CompanyDepart */



$this->menu=$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<h1>View CompanyDepart #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'text',
		'titleSeo',
		'nameT',
	),
)); ?>
