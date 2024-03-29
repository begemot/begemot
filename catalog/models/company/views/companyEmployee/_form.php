<?php
/* @var $this CompanyEmployeeController */
/* @var $model CompanyEmployee */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'company-employee-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'position'); ?>
		<?php echo $form->textField($model,'position',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'position'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php
		$this->widget('begemot.extensions.ckeditor.CKEditor',
			//$this->widget('CKEditor',
			//        $this->widget('//home/atv/www/atvargo.ru/protected/extensions/ckeditor/CKEditor',
			array('model' => $model, 'attribute' => 'text', 'language' => 'ru', 'editorTemplate' => 'full',));
		?>
		<?php echo $form->error($model,'text'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->