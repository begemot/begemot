<?php
echo 123;
$this->breadcrumbs=array(
	'Seo Pages'=>array('index'),
	$model->title,
);
$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
?>

<h1>View SeoPages #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'url',
		'title',
		'content',
		'status',
	),
)); ?>
