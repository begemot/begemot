<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cat-category-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'pid'); ?>
		<?php 
                $select =  ($model->isNewRecord ? '-1' : $model->pid);
                $listArray = CHtml::listData($model->findAll(),'id','name');
                $listArray[-1]='корневой уровень';
                   echo CHtml::dropDownList('CatCategory[pid]', $select,
              $listArray,
              array('empty' => '(Выберите категорию)'));
                ?>
		
		<?php echo $form->error($model,'pid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>70)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php 
                        $this->widget('begemot.extensions.ckeditor.CKEditor',
       //$this->widget('CKEditor',
        //        $this->widget('//home/atv/www/atvargo.ru/protected/extensions/ckeditor/CKEditor', 
                array('model' => $model, 'attribute' => 'text', 'language' => 'ru', 'editorTemplate' => 'full',));
                ?>
		<?php echo $form->error($model,'text'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'seo_title'); ?>
		<?php echo $form->textField($model,'seo_title',array('size'=>60,'maxlength'=>70)); ?>
		<?php echo $form->error($model,'seo_title'); ?>
	</div>



	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget();
        
        $picturesConfig = array();
        $configFile = Yii::getPathOfAlias('webroot').'/protected/config/catalog/categoryPictureSettings.php';
        if (file_exists($configFile)){
            
            $picturesConfig = require($configFile);
            
            $this->widget(
                'application.modules.pictureBox.components.PictureBox', array(
                'id' => 'catalogCategory',
                'elementId' => $model->id,
                'config' => $picturesConfig,
                    )
            );
        } else{
            Yii::app()->user->setFlash('error','Отсутствует конфигурационный файл:'.$configFile);
        }
?>

</div><!-- form -->