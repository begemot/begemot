<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'posts-form',
        'enableAjaxValidation' => false,
            ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 100)); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'title_seo'); ?>
        <?php echo $form->textField($model, 'title_seo', array('size' => 60, 'maxlength' => 100)); ?>
        <?php echo $form->error($model, 'title_seo'); ?>
    </div>
    <div class="row">
        <?php
            echo $form->labelEx($model, 'text');
            echo '<div style="text-align:right;">';
            $this->widget('bootstrap.widgets.TbButton',array(
                'label' => 'Расставить изображения',
                'type' => 'primary',
                'size' => 'mini',
                'url'=>array('default/tidyPost','id'=>$model->id)
            ));
            echo '</div>';
        ?>

        
            <?php 
                    $this->widget('begemot.extensions.ckeditor.CKEditor',
            array('model' => $model, 'attribute' => 'text', 'language' => 'ru', 'editorTemplate' => 'full',));
            ?>

            <?php
                $this->widget('begemot.components.htmlClearPanel.htmlClearPanel',array('id'=>'Posts_text'));
            ?>

        <?php echo $form->error($model, 'text'); ?>
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
    <div class="row">
        <label for="Posts_author">Раздел</label>
        <?php
        $tags = PostsTags::getTags();
        echo $form->dropDownList($model, 'tag_id', $tags);
        ?>
        <?php echo $form->error($model, 'author'); ?>
    </div>


   
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget(); ?>

    <?php
    
    require Yii::getPathOfAlias('webroot').'/protected/config/postConfig.php';

    if (!$model->isNewRecord)
        $this->widget(
                'application.modules.pictureBox.components.PictureBoxFiles', array(
            'id' => 'posts',
            'elementId' => $model->id,
            'config' => $picturesConfig,
                )
        );

    ?>

</div><!-- form -->