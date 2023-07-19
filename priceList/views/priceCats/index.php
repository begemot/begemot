<?php
/* @var $this PriceCatsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Price Cats',
);

$this->menu=require (dirname(__FILE__).'/../commonMenu.php');
?>

<h1>Price Cats</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
