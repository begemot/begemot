<div class="form">


<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'video-gallery-video-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		Fields with <span class="required">*</span> are required.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model, 'title'); ?>
		<?php echo $form->error($model,'title'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php echo $form->textArea($model, 'text'); ?>
		<?php echo $form->error($model,'text'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textField($model, 'url'); ?>
		<?php echo $form->error($model,'url'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'gallery_id'); ?>
		<?php echo $form->dropDownList($model, 'gallery_id', GxHtml::listDataEx(VideoGallery::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'gallery_id'); ?>
		</div><!-- row -->

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
<?php
echo GxHtml::submitButton('Save');
$this->endWidget();
?>
</div><!-- form -->

    <?php
    
    $videoConfig = require Yii::getPathOfAlias('webroot').'/protected/config/videoConfig.php';

    if (!$model->isNewRecord)
        $this->widget(
                'application.modules.pictureBox.components.PictureBoxFiles', array(
            'id' => 'videoGallery',
            'elementId' => $model->id,
            'config' => $videoConfig,
                )
        );
    ?>