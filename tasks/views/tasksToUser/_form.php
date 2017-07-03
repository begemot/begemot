<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'tasks-to-user-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		Fields with <span class="required">*</span> are required.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'video_link'); ?>
		<?php echo $form->textField($model, 'video_link', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'video_link'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php echo $form->textArea($model, 'text'); ?>
		<?php echo $form->error($model,'text'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->textField($model, 'price'); ?>
		<?php echo $form->error($model,'price'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'update_time'); ?>
		<?php echo $form->textField($model, 'update_time', array('maxlength' => 45)); ?>
		<?php echo $form->error($model,'update_time'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'create_time'); ?>
		<?php echo $form->textField($model, 'create_time'); ?>
		<?php echo $form->error($model,'create_time'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model, 'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton('Save');
$this->endWidget();
?>
</div><!-- form -->