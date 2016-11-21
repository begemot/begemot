<?php
/* @var $this CompanyDepartController */
/* @var $model CompanyDepart */



$this->menu=$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<h1>Create CompanyDepart</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>