<?php
/* @var $this PriceCatsController */
/* @var $model PriceCats */



$this->menu=require (dirname(__FILE__).'/../commonMenu.php');
?>

<h1>Создаем категорию цен</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>