<?php
/* @var $this PriceCatsController */
/* @var $model PriceCats */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'price-cats-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php


    echo $form->errorSummary($model); ?>

	<div class="row">
		<?php
			$options = PriceCats::model()->findAll(['condition'=>'level=0']);
		$optionsArray = ['-1'=>'корневой уровень'];
		foreach ($options as $option){
			$optionsArray[(string)$option->id] = $option->name;

		}

		?>
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textArea($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pid'); ?>
		<?php echo $form->dropDownList($model,'pid',$optionsArray); ?>
		<?php echo $form->error($model,'pid'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->