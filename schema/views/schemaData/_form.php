<?php
/* @var $this SchemaLinksController */
/* @var $model SchemaLinks */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'schema-links-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'linkType'); ?>
		<?php echo $form->textField($model,'linkType',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'linkType'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'linkId'); ?>
		<?php echo $form->textField($model,'linkId'); ?>
		<?php echo $form->error($model,'linkId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'schemaId'); ?>
		<?php echo $form->textField($model,'schemaId'); ?>
		<?php echo $form->error($model,'schemaId'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->