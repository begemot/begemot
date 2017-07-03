
<?php $form = $this->beginWidget('UActiveForm', array(
	'id' => 'tasks-form',
	'enableAjaxValidation' => true,

	'clientOptions'=>array(
		'validateOnSubmit'=>true,
		'afterValidate' => 'js: validateAjaxForm'
		//'afterValidate' => 'js: function(form, data, hasError) {alert("data")}'
	),
));
?>
	<?php echo $form->errorSummary($model) ?>
	<div class="hide">
		<?php echo $form->error($model,'title'); ?>
		<?php echo $form->error($model,'image'); ?>
		<?php echo $form->error($model,'text'); ?>
	</div>

	<div class='info'></div>
	<div class="success"></div>

	<div class="form-group">
		<?php echo $form->textField($model,'title', array('placeholder' => 'Заголовок', 'class' => 'form-control')); ?>
    </div>
	<div class="form-group">
		<?php echo $form->textarea($model,'text', array('placeholder' => 'Описание', 'class' => 'form-control', 'rows' => 5)); ?>
	</div>
	<?php 
		echo $form->hiddenField($model,'image', array("class" => 'image_holder_form'));
        echo $form->fileField($model,'image_field',array('id'=>'image_field', 'style' => 'display:none')); // image file select when clicks on upload photo
    ?>
    <?php if ($model->image): ?>
    	<div id="image_holder" style="display: block;"><img src="<?php echo $model->image . "?" . time()?>"></div>
    	<input type="hidden" name='new_picture' id='new_picture' value='0' />
    <?php else: ?>
    	<div id='image_holder'></div>
    	<input type="hidden" name='new_picture' id='new_picture' value='1' />
    <?php endif ?>
	
	<div class="form-group file-group file-input">
        <a href="#" class="btn btn-border uploadImageTemp">обзор</a>
    </div>
    <p id='image_error' class='hide'></p>
    <p>Рекомендуемый размер изображения 1024*768 (максимальный размер 2мб)</p>
    
	<input type="hidden" name='aspectRatio' value='1.53' id='aspectRatio'/>
    <input type="hidden" name='cords_x1' id='cords_x1'/>
    <input type="hidden" name='cords_x2' id='cords_x2'/>
    <input type="hidden" name='cords_y1' id='cords_y1'/>
    <input type="hidden" name='cords_y2' id='cords_y2'/>
    <input type="hidden" name='cords_w' id='cords_w'/>
    <input type="hidden" name='cords_h' id='cords_h'/>
    <?php if (isset($image_width)): ?>
    	<input type="hidden" name='image_width' id='image_width' value='<?php echo $image_width?>'/>
    <?php endif ?>
    <input type="hidden" name='current_w' id='current_w' />
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить задание' : 'Изменить задание', array('class' => 'btn btn-border')); ?>

<?php $this->endWidget();?>