<?php
/* @var $this CatItemController */
/* @var $model CatItem */
/* @var $form CActiveForm */
?>



<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'cat-item-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля со знаком <span class="required">*</span> обязательны для заполнения.</p>
        
        <div  class="container-fluid">  
	
        <?php echo $form->errorSummary($model); ?>
        
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->textField($model,'price'); ?>
		<?php echo $form->error($model,'price'); ?>
	</div>
        
	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
        <?php

        echo '<div style="text-align:right;">';
        $this->widget('bootstrap.widgets.TbButton',array(
            'label' => 'Расставить изображения',
            'type' => 'primary',
            'size' => 'mini',
            'url'=>array('catItem/tidyItemText','id'=>$model->id)
        ));
        echo '</div>';
        ?>
		<?php 
                        $this->widget('begemot.extensions.ckeditor.CKEditor',
       //$this->widget('CKEditor',
        //        $this->widget('//home/atv/www/atvargo.ru/protected/extensions/ckeditor/CKEditor', 
                array('model' => $model, 'attribute' => 'text', 'language' => 'ru', 'editorTemplate' => 'full',));
                ?>
            		<?php 
                        $this->widget('begemot.components.htmlClearPanel.htmlClearPanel',array('id'=>'CatItem_text'));
                ?>
		<?php echo $form->error($model,'text'); ?>
	</div>   
        <div class="row">
		<?php echo $form->labelEx($model,'seo_title'); ?>
		<?php echo $form->textField($model,'seo_title',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'seo_title'); ?>
	</div>    
        <?php
        
        $itemAdditionalRows = CatItemsRow::model()->findAll();
        
         if (is_array($itemAdditionalRows)){
                
                foreach($itemAdditionalRows as $itemRow){
                    echo '<div class="row">';
                    if ($itemRow->type=='text'){
                        echo CHtml::label($itemRow->name, $itemRow->name_t);
                        $this->widget('begemot.extensions.ckeditor.CKEditor',array('model' => $model, 'attribute' => $itemRow->name_t, 'language' => 'ru', 'editorTemplate' => 'full',));
                    } else{
                    echo CHtml::label($itemRow->name, $itemRow->name_t);
                    echo $form->textArea($model,$itemRow->name_t);

                    }
                       echo '</div>  ';
                }
               
         }
        ?>
    
    <div class="row">
        <?php echo $form->labelEx($model,'quantity'); ?>
        <?php echo $form->textField($model,'quantity'); ?>
        <?php echo $form->error($model,'quantity'); ?>
    </div>   
    <div class="row">
        <?php echo $form->labelEx($model,'article'); ?>
        <?php echo $form->textField($model,'article'); ?>
        <?php echo $form->error($model,'article'); ?>
    </div>   
    
    <div class="row">
        <?php echo $form->labelEx($model,'delivery_date'); ?>
        <?php $this->widget(
            'bootstrap.widgets.TbDatePicker',
            array(
                'model'=>$model,
                'attribute' => 'delivery_date',

            )
        ); ?>
    </div>    
    <div class="row">
        <?php
        $this->widget('begemot.extensions.contentKit.widgets.KitFormPart',
            array(
                'form'=>$form,
                'model'=>$model
            )
        );

        ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>



        
        
        
</div><!-- form -->