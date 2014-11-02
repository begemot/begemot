<?php
/* @var $this FaqCatsController */
/* @var $model FaqCats */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'faq-cats-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?php echo Yii::t('FaqModule.faq','Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textArea($model,'name',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('FaqModule.faq','Create') : Yii::t('FaqModule.faq','Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->