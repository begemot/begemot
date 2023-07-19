<?php
/* @var $this CatShipmentController */
/* @var $model CatShipment */

$this->breadcrumbs=array(
	'Cat Shipments'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List CatShipment', 'url'=>array('index')),
	array('label'=>'Create CatShipment', 'url'=>array('create')),
	array('label'=>'View CatShipment', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage CatShipment', 'url'=>array('admin')),
);
?>

<h1>Update CatShipment <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>