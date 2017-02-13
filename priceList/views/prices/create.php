<?php
/* @var $this PricesController */
/* @var $model Prices */

$this->menu = require(dirname(__FILE__) . '/../commonMenu.php');


?>

<h1>Create Prices</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>