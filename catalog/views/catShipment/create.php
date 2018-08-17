<?php
/* @var $this CatShipmentController */
/* @var $model CatShipment */


$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';
?>

<h1>Create CatShipment</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>