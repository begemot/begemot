<?php
/* @var $this PromoController */
/* @var $model Promo */

$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';
?>

<h1>Создание акции</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>