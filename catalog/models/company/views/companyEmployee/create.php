<?php
/* @var $this CompanyEmployeeController */
/* @var $model CompanyEmployee */



$this->menu=$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<h1>Create CompanyEmployee</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>