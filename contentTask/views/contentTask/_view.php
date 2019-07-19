<?php
/* @var $this ContentTaskController */
/* @var $data ContentTask */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text')); ?>:</b>
	<?php echo CHtml::encode($data->text); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('actionsList')); ?>:</b>
	<?php echo CHtml::encode($data->actionsList); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dataElementsList')); ?>:</b>
	<?php echo CHtml::encode($data->dataElementsList); ?>
	<br />


</div>