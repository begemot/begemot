<?php
/* @var $this CatOrderController */
/* @var $model CatOrder */


$this->menu = require dirname(__FILE__).'/../catItem/commonMenu.php';
?>

<h1>Create CatOrder</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>