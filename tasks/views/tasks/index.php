<?php

$this->breadcrumbs = array(
	Tasks::label(2),
	'Index',
);

$this->menu = require dirname(__FILE__).'/../commonMenu.php';
?>

<h1><?php echo GxHtml::encode(Tasks::label(2)); ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); 