<?php
/* @var $this DiscountController */
/* @var $model Discount */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'discount-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
	
    <div class="row">
        <?php echo $form->labelEx($model, 'minSale'); ?>
        <?php echo $form->textField($model, 'minSale'); ?>
        <?php echo $form->error($model, 'minSale'); ?>


    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'sale'); ?>
        <?php echo $form->textField($model, 'sale'); ?>
        <?php echo $form->error($model, 'sale'); ?>


    </div>


    <div class="row">
        <?php echo $form->labelEx($model, 'maxSale'); ?>
        <?php echo $form->textField($model, 'maxSale'); ?>
        <?php echo $form->error($model, 'maxSale'); ?>


    </div>
    

    <div class="row">
        <?php 
        echo $form->labelEx($model,'active');
             echo $form->checkBoxRow($model,'active',array());
         ?>
            
    </div>
    <!--<div class="row">
        <?php
        $this->widget('begemot.extensions.contentKit.widgets.KitFormPart',
            array(
                'form' => $form,
                'model' => $model
            )
        );
        ?>
    </div> -->
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>


</div><!-- form -->