<?php
/* @var $this CatItemsRowController */
/* @var $model CatItemsRow */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cat-items-row-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>



	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php 
                $select =  ($model->isNewRecord ? '-1' : $model->type);
           
                $listArray['string']='Строка 255';
                $listArray['text']='text';
                $listArray['radioList']='радио список';
                $listArray['checkboxList']='чекбокс список';
                $listArray['select']='выпадающий список';
                $listArray['selectMultiple']='выпадающий список с несколькими значениями';
                   echo CHtml::dropDownList('CatItemsRow[type]', $select,
              $listArray,
              array('empty' => '(Выберите тип)'));
                ?>
		
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
<br/>
	<div>
		<h2>Описание работы:</h3>

		<p>Все названия полей вводятся по алгоритму Название|значение поля, если требуется несколько значений, то Название|#значение_поля1#значение_поля2 и так далее</p>

		<h3>Примеры:</h3>

		<p>Текстовое поле с начальным значением в поле "Text" - название|Text</p>
		<p>Чеклист - Название|#значение_поля1#значение_поля2</p>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->