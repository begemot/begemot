<?php
/* @var $this CatItemController */
/* @var $model CatItem */
/* @var $form CActiveForm */
?>



<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'cat-item-form',
    'enableAjaxValidation' => false,
)); ?>

<p class="note">Поля со знаком <span class="required">*</span> обязательны для заполнения.</p>


<div class="container-fluid">

    <?php echo $form->errorSummary($model); ?>
    <?php if (Yii::app()->controller->action->id == 'update' && Yii::app()->user->isAdmin()) { ?>
        <div class="row">
            <?php echo $form->labelEx($model, 'authorId'); ?>


            <?php

            // Yii::import('user.models.User');
            $models = User::model()->findAll();
            $list = CHtml::listData($models,
                'id', 'username');
            echo CHtml::dropDownList('CatItem[authorId]', $model->authorId,
                $list,
                array('empty' => '(Выберите пользователя'));

            ?>

            <?php echo $form->error($model, 'authorId'); ?>
        </div>
    <?php } ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 100)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'price'); ?>
        <?php echo $form->textField($model, 'price'); ?>
        <?php echo $form->error($model, 'price'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'text'); ?>
        <?php

        echo '<div style="text-align:right;">';
        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Расставить изображения',
            'type' => 'primary',
            'size' => 'mini',
            'url' => array('catItem/tidyItemText', 'id' => $model->id)
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
        $this->widget('begemot.components.htmlClearPanel.htmlClearPanel', array('id' => 'CatItem_text'));
        ?>
        <?php echo $form->error($model, 'text'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'seo_title'); ?>
        <?php echo $form->textField($model, 'seo_title', array('size' => 60, 'maxlength' => 100)); ?>
        <?php echo $form->error($model, 'seo_title'); ?>
    </div>
    <?php

    $itemAdditionalRows = CatItemsRow::model()->findAll();

    if (is_array($itemAdditionalRows)) {

        foreach ($itemAdditionalRows as $itemRow) {
            echo '<div class="row">';
            $options = explode("|", $itemRow->name);
            $name = $options[0];
            @$value = $options[1];

            $options = explode("#", $value);
            $options = array_filter($options);
            $array = array();
            foreach ($options as $option) {
                $array[$option] = $option;
            }
            $options = $array;

            if ($itemRow->type == 'text') {
                $name_of_field = $itemRow->name_t;
                if (isset($model->$name_of_field) && $model->$name_of_field == "") {
                    $model->$name_of_field = $value;
                }
                echo CHtml::label($name, $itemRow->name_t);
                $this->widget('begemot.extensions.ckeditor.CKEditor', array('model' => $model, 'value' => $value, 'attribute' => $itemRow->name_t, 'language' => 'ru', 'editorTemplate' => 'full',));
                $this->widget('begemot.components.htmlClearPanel.htmlClearPanel', array('id' => 'CatItem_'.$itemRow->name_t));
            } else if ($itemRow->type == 'checkboxList') {
                $select = array();
                $name_of_field = $itemRow->name_t;
                if (isset($model->$name_of_field)) {
                    $select = explode(",", $model->$name_of_field);
                }
                echo CHtml::label($name, $itemRow->name_t);
                echo CHtml::checkBoxList("CatItem[" . $itemRow->name_t . "][]", $select, $options, array("template" => '<label class="checkbox">{input} {label}</label>', 'separator' => ''));
            } else if ($itemRow->type == 'selectMultiple') {
                $select = array();
                $name_of_field = $itemRow->name_t;
                if (isset($model->$name_of_field)) {
                    $select = explode(",", $model->$name_of_field);
                }
                echo CHtml::label($name, $itemRow->name_t);
                echo CHtml::listBox("CatItem[" . $itemRow->name_t . "][]", $select, $options, array('multiple' => true));
            } else if ($itemRow->type == 'radioList') {
                echo $form->radioButtonListRow($model, $itemRow->name_t, $options, array('labelOptions' => array('label' => $name)));
            } else if ($itemRow->type == 'select') {
                echo $form->dropDownListRow($model, $itemRow->name_t, $options, array('labelOptions' => array('label' => $name)));
            } else {
                $name_of_field = $itemRow->name_t;
                if (isset($model->$name_of_field) && $model->$name_of_field == "") {
                    $model->$name_of_field = $value;
                }

                echo CHtml::label($name, $itemRow->name_t);
                echo $form->textArea($model, $itemRow->name_t);
            }
            echo '</div>  ';
        }

    };?>

    <div class="row">
        <?php echo $form->labelEx($model, 'quantity'); ?>
        <?php echo $form->textField($model, 'quantity'); ?>
        <?php echo $form->error($model, 'quantity'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'article'); ?>
        <?php echo $form->textField($model, 'article'); ?>
        <?php echo $form->error($model, 'article'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'delivery_date'); ?>
        <?php $this->widget(
            'bootstrap.widgets.TbDatePicker',
            array(
                'model' => $model,
                'attribute' => 'delivery_date',
            )
        ); ?>
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


</div><!-- form -->
