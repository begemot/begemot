<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	GxHtml::valueEx($model) => array('view', 'id' => GxActiveRecord::extractPkValue($model, true)),
	'Update',
);

$this->menu = require(dirname(__FILE__).'/../commonMenu.php');
?>

<h1><?php echo 'Update' . ' ' . GxHtml::encode($model->label()) . ' '; ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model));
?>