<?php
/* @var $this InvoicesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Tasks Invoices',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1>Tasks Invoices</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
