<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'posts-tags-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'tag_name'); ?>
		<?php echo $form->textField($model,'tag_name',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'tag_name'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'title_seo'); ?>
		<?php echo $form->textField($model,'title_seo',array('size'=>20,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'title_seo'); ?>
	</div>
    <div class="row">
        <?php echo $form->labelEx($model,'info'); ?>
        <?php
        $this->widget('begemot.extensions.ckeditor.CKEditor',
            array('model' => $model, 'attribute' => 'info', 'language' => 'ru', 'editorTemplate' => 'full',));
        ?>

        <?php echo $form->error($model,'info'); ?>
    </div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->