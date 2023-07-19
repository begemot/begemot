<?php
/* @var $this PromoController */
/* @var $model Promo */

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';


?>

<h1>View Discount #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'title',
		'minSale',
		'sale',
		'maxSale',
		'active'
	),
)); ?>
