<?php
/* @var $this ParsersCategoryConnectionController */
/* @var $model ParsersCategoryConnection */

$this->breadcrumbs=array(
	'Parsers Category Connections'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Изменение',
);

require(dirname(__FILE__).'/../menu.php');
?>

<h1>Update ParsersCategoryConnection <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>