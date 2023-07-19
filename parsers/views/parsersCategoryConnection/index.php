<?php
/* @var $this ParsersCategoryConnectionController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Parsers Category Connections',
);

require(dirname(__FILE__).'/../menu.php');
?>

<h1>Parsers Category Connections</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
