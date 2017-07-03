<?php
/* @var $this InvoicesController */
/* @var $model TasksInvoice */

$this->breadcrumbs=array(
	'Tasks Invoices'=>array('index'),
	$model->id,
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>View TasksInvoice #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
		'amount',
		'create_at',
		'paid_at',
		'user_id',
		'task_id',
	),
)); ?>
