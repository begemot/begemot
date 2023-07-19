<?php
/* @var $this LoginFormController */
/* @var $model LoginForm */
/* @var $form CActiveForm */
$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
?>



<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'new-page-test-form',
	'enableAjaxValidation'=>false,
        
)); ?>

        <h1>Создать статическую страницу</h1>
<div class="container-fluid offset">
	<?php echo $form->errorSummary($model,'Исправьте следующие ошибки:'); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textField($model,'url'); ?>
		<?php echo $form->error($model,'url'); ?>
	</div>
    <div class="row">
        <?php echo $form->labelEx($model,'title'); ?>
        <?php echo $form->textField($model,'title'); ?>
        <?php echo $form->error($model,'title'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'keywords'); ?>
        <?php echo $form->textField($model,'keywords'); ?>
        <?php echo $form->error($model,'keywords'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textField($model,'description'); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->