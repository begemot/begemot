<?php
/* @var $this SliderController */
/* @var $model Slider */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
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

    <?php echo $form->textAreaRow($model,'header',array('rows'=>6, 'cols'=>50)); ?>


    <?php echo $form->textAreaRow($model,'text1',array('rows'=>6, 'cols'=>50)); ?>



    <?php echo $form->textAreaRow($model,'text2',array('rows'=>6, 'cols'=>50)); ?>



    <?php echo $form->textAreaRow($model,'text3',array('rows'=>6, 'cols'=>50)); ?>


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('SliderModule.msg','Create') : Yii::t('SliderModule.msg','Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->