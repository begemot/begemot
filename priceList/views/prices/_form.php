<?php
/* @var $this PricesController */
/* @var $model Prices */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'prices-form',
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
		<?php

		$catsModels = PriceCats::model()->findAll();
		$catssArray=['-1'=>'без категории'];
		foreach ($catsModels as $catModel){
			$catssArray[$catModel->id] = $catModel->name;
		}

			echo $form->labelEx($model,'catId');
		?>
		<?php echo $form->dropDownList($model,'catId',$catssArray); ?>
		<?php echo $form->error($model,'catId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->textField($model,'price'); ?>
		<?php echo $form->error($model,'price'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'text'); ?>
        <?php echo $form->textArea($model,'text'); ?>
        <?php echo $form->error($model,'text'); ?>
    </div>

	<div class="row">
		<?php

		$typesArray=[
			'цена'=>"цена",
			'подкатегория'=>"подкатегория",
		];

				echo $form->labelEx($model,'type');
		?>
		<?php echo $form->dropDownList($model,'type',$typesArray); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>



	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->