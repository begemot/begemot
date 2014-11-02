<?php
/* @var $this SliderController */
/* @var $data Slider */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('image')); ?>:</b>
	<?php echo CHtml::encode($data->image); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('header')); ?>:</b>
	<?php echo CHtml::encode($data->header); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text1')); ?>:</b>
	<?php echo CHtml::encode($data->text1); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text2')); ?>:</b>
	<?php echo CHtml::encode($data->text2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text3')); ?>:</b>
	<?php echo CHtml::encode($data->text3); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('order')); ?>:</b>
	<?php echo CHtml::encode($data->order); ?>
	<br />


</div>