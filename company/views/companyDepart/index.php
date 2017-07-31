<?php
/* @var $this CompanyDepartController */
/* @var $dataProvider CActiveDataProvider */



$this->menu=$this->menu=require dirname(__FILE__) . '/../default/commonMenu.php';
?>

<h1>Company Departs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
