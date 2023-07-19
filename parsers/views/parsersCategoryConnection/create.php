<?php
/* @var $this ParsersCategoryConnectionController */
/* @var $model ParsersCategoryConnection */

$this->breadcrumbs=array(
	'Parsers Category Connections'=>array('index'),
	'Создание',
);

require(dirname(__FILE__).'/../menu.php');
?>

<h1>Create ParsersCategoryConnection</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>