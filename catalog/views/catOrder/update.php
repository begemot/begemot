<?php
/* @var $this CatOrderController */
/* @var $model CatOrder */

$this->breadcrumbs=array(
	'Cat Orders'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List CatOrder', 'url'=>array('index')),
	array('label'=>'Create CatOrder', 'url'=>array('create')),
	array('label'=>'View CatOrder', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage CatOrder', 'url'=>array('admin')),
);
?>

<h1>Update CatOrder <?php echo $model->id; ?></h1>

<?php

	foreach ($model->orderItems as $orderItem){
		$itemId =  $orderItem->itemId;
		$catItem = CatItem::model()->findByPk($itemId);
		echo $catItem->id.' '.$catItem->name.' в количестве '.$orderItem->count.' <br>';
	}

?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>