<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	GxHtml::valueEx($model) => array('view', 'id' => GxActiveRecord::extractPkValue($model, true)),
	'Update',
);

$this->menu = array(
	array('label' => 'List' . ' ' . $model->label(2), 'url'=>array('index')),
	array('label' => 'Create' . ' ' . $model->label(), 'url'=>array('create')),
	array('label' => 'View' . ' ' . $model->label(), 'url'=>array('view', 'id' => GxActiveRecord::extractPkValue($model, true))),
	array('label' => 'Manage' . ' ' . $model->label(2), 'url'=>array('admin')),
);
?>
<div class="popup__block zoom-anim-dialog popup-dialog" style='max-width: 420px'>
	<h1>Изменение задания "<?php echo $model->title ?>"</h1>

	<?php
	$this->renderPartial('_form', array(
		'model' => $model,
		'image_width' => $image_width
	));
	?>
</div>