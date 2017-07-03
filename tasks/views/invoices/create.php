<?php
/* @var $this InvoicesController */
/* @var $model TasksInvoice */

$this->breadcrumbs=array(
	'Tasks Invoices'=>array('index'),
	'Create',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Create TasksInvoice</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>