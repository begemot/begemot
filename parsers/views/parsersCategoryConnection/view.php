<?php
/* @var $this ParsersCategoryConnectionController */
/* @var $model ParsersCategoryConnection */

$this->breadcrumbs=array(
	'Parsers Category Connections'=>array('index'),
	$model->id,
);

require(dirname(__FILE__).'/../menu.php');
?>

<h1>View ParsersCategoryConnection #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'connect_name',
		'category_id',
	),
)); ?>
