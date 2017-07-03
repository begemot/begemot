<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	'Create',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1><?php echo 'Create' . ' ' . GxHtml::encode($model->label()); ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model,
		'buttons' => 'create'));
?>