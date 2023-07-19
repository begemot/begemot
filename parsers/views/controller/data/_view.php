<?php
/* @var $this DataController */
/* @var $data WebParser */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php echo CHtml::encode($data->date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('report_text')); ?>:</b>
	<?php echo CHtml::encode($data->report_text); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('processTime')); ?>:</b>
	<?php echo CHtml::encode($data->processTime); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pagesProcessed')); ?>:</b>
	<?php echo CHtml::encode($data->pagesProcessed); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />


</div>