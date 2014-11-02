<?php
/* @var $this FaqController */
/* @var $model Faq */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'faq-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?php echo Yii::t('FaqModule.faq','Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>
   <?php if($model->isNewRecord): ?>
      <div class="row">
         <?php echo $form->labelEx($model,'name'); ?>
         <?php echo $form->textField($model,'name'); ?>
         <?php echo $form->error($model,'name'); ?>
      </div>

      <div class="row">
         <?php echo $form->labelEx($model,'email'); ?>
         <?php echo $form->textField($model,'email'); ?>
         <?php echo $form->error($model,'email'); ?>
      </div>

      <div class="row">
         <?php echo $form->labelEx($model,'phone'); ?>
         <?php echo $form->textField($model,'phone'); ?>
         <?php echo $form->error($model,'phone'); ?>
      </div>
   <?php endif; ?>
	<div class="row">
		<?php echo $form->labelEx($model,'question'); ?>
		<?php echo $form->textArea($model,'question',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'question'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'answer'); ?>
		<?php echo $form->textArea($model,'answer',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'answer'); ?>
	</div>
	<div class="row">
		<?php        $this->widget('begemot.extensions.contentKit.widgets.KitFormPart',
            array(
                'form'=>$form,
                'model'=>$model
            )
        ); ?> 
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cid'); ?>
		<?php echo $form->dropDownList($model,'cid', CHtml::listData(FaqCats::model()->findAll(), 'id', 'name'), array('prompt'=>'Выберите раздел'),array('options'=>array($model->cid=>array('selected'=>'selected')))); ?>
		<?php echo $form->error($model,'cid'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('FaqModule.faq','Create') : Yii::t('FaqModule.faq','Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->