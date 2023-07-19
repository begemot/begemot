<?php
/* @var $this PromoController */
/* @var $model Promo */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'promo-form',
        'enableAjaxValidation' => false,
    )); ?>

    <p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

    <?php echo $form->errorSummary($model); ?>


    <?php echo $form->textFieldRow($model, 'title', array('size' => 60, 'maxlength' => 100)); ?>

    <?php echo $form->textFieldRow($model, 'title2', array('size' => 60, 'maxlength' => 100)); ?>
    <?php echo $form->textFieldRow($model, 'title3', array('size' => 60, 'maxlength' => 100)); ?>
    <?php echo $form->textFieldRow($model, 'sale', array('size' => 60, 'maxlength' => 100)); ?>
    <?php
    if ($model->dateFrom==''){
        $date = '';
    } else {
        $date= date ('d.m.Y',$model->dateFrom);
    }
    echo $form->datepickerRow($model, 'dateFrom', array('size' => 60, 'maxlength' => 100,'value'=>$date)); ?>
    <?php
    if ($model->dateTo==''){
        $date = '';
    } else {
        $date= date ('d.m.Y',$model->dateTo);
    }

    echo $form->datepickerRow($model, 'dateTo', array('size' => 60, 'maxlength' => 100,'value'=>$date)); ?>
    <div class="row">
        <?php echo $form->labelEx($model, 'text'); ?>


        <?php

        $this->widget('begemot.extensions.ckeditor.CKEditor',
            //$this->widget('CKEditor',
            //        $this->widget('//home/atv/www/atvargo.ru/protected/extensions/ckeditor/CKEditor',
            array('model' => $model, 'attribute' => 'text', 'language' => 'ru', 'editorTemplate' => 'full',));
        ?>

        <?php echo $form->error($model, 'text'); ?>


    </div>
    <div class="row">
        <?php
        $this->widget('begemot.extensions.contentKit.widgets.KitFormPart',
            array(
                'form' => $form,
                'model' => $model
            )
        );
        ?>
    </div>
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget(); ?>




    <?php

    $picturesConfig = array();

    $defaultConfig = [
        'divId' => 'pictureBox',
        'nativeFilters' => array(

            'standart' => true,

        ),
        'filtersTitles' => array(

            'standart' => 'основное',

        ),
        'imageFilters' => array(

            'standart' => array(
                1 => array(
                    'filter' => 'CropResizeUpdate',
                    'param' => array(
                        'width' => 200,
                        'height' => 200,
                    ),
                ),
            ),
        ),
    ];

    if (!$model->isNewRecord) {
        $configFile = Yii::getPathOfAlias(CatalogModule::PROMO_CONFIG_FILE_ALIAS);
        if (file_exists($configFile . 'argoBtns.php')) {


            $picturesConfig = require($configFile . 'argoBtns.php');
        } else {
            $picturesConfig = $defaultConfig;
        }

        $this->widget(
            'application.modules.pictureBox.components.PictureBox', array(
                'id' => 'catalogPromo',
                'elementId' => $model->id,
                'config' => $picturesConfig,
            )
        );
    }
    ?>


</div><!-- form -->