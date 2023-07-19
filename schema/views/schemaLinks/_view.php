<?php
/* @var $this SchemaLinksController */
/* @var $data SchemaLinks */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('linkType')); ?>:</b>
	<?php echo CHtml::encode($data->linkType); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('linkId')); ?>:</b>
	<?php echo CHtml::encode($data->linkId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('schemaId')); ?>:</b>
	<?php echo CHtml::encode($data->schemaId); ?>
	<br />


</div>