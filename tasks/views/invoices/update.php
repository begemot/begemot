<?php
/* @var $this InvoicesController */
/* @var $model TasksInvoice */

$this->breadcrumbs=array(
	'Tasks Invoices'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Update TasksInvoice <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>