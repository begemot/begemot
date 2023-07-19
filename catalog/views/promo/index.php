<?php
/** @var $this PromoController */
/** @var $dataProvider CActiveDataProvider */

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';
?>

<h1>Promos</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
