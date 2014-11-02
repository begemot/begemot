<?php
/* @var $this SliderController */
/* @var $model Slider */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'slider-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
   'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'image'); ?>
		<?php echo $form->fileField($model,'image'); ?>
		<?php echo $form->error($model,'image'); ?>
	</div>
   <?php if(!$model->isNewRecord): ?>
      <?php echo CHtml::image($model->image); ?>
   <?php endif; ?>
	<div class="row">
		<?php echo $form->labelEx($model,'header'); ?>
		<?php echo $form->textArea($model,'header',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'header'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'text1'); ?>
		<?php echo $form->textArea($model,'text1',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'text1'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'text2'); ?>
		<?php echo $form->textArea($model,'text2',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'text2'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'text3'); ?>
		<?php echo $form->textArea($model,'text3',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'text3'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('SliderModule.msg','Create') : Yii::t('SliderModule.msg','Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->