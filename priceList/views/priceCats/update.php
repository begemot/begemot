<?php
/* @var $this PriceCatsController */
/* @var $model PriceCats */

$this->breadcrumbs=array(
	'Price Cats'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=require (dirname(__FILE__).'/../commonMenu.php');
?>

<h1>Update PriceCats <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>