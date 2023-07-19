<?php
/* @var $this CompanyDepartController */
/* @var $model CompanyDepart */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'company-depart-form',
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
		<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'name'); ?>
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

	<div class="row">
		<?php echo $form->labelEx($model,'titleSeo'); ?>
		<?php echo $form->textField($model,'titleSeo',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'titleSeo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nameT'); ?>
		<?php echo $form->textField($model,'nameT',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'nameT'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->