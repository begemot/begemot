<?php
/* @var $this SchemaLinksController */
/* @var $model SchemaLinks */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'linkType'); ?>
		<?php echo $form->textField($model,'linkType',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'linkId'); ?>
		<?php echo $form->textField($model,'linkId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'schemaId'); ?>
		<?php echo $form->textField($model,'schemaId'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->