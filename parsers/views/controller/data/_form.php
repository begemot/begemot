<?php
/* @var $this DataController */
/* @var $model WebParser */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'web-parser-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'date'); ?>
		<?php echo $form->textField($model,'date'); ?>
		<?php echo $form->error($model,'date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'report_text'); ?>
		<?php echo $form->textArea($model,'report_text',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'report_text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'processTime'); ?>
		<?php echo $form->textField($model,'processTime'); ?>
		<?php echo $form->error($model,'processTime'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pagesProcessed'); ?>
		<?php echo $form->textField($model,'pagesProcessed'); ?>
		<?php echo $form->error($model,'pagesProcessed'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->