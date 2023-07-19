<?php
/* @var $this WebParserController */
/* @var $model WebParser */

$this->breadcrumbs=array(
	'Web Parsers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List WebParser', 'url'=>array('index')),
	array('label'=>'Manage WebParser', 'url'=>array('admin')),
);
?>

<h1>Create WebParser</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>