<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'cat-category-form',
        'enableAjaxValidation' => false,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'pid'); ?>
        <?php
        $select = ($model->isNewRecord ? '-1' : $model->pid);
       // $listArray = CHtml::listData($model->findAll(array("order" => "`order`")), 'id', 'name');
        $listArray = [];
        $cats = $model->findAll(array("order" => "`order`"));
        foreach ($cats as $cat) {
            $listArray[$cat->id] = str_repeat('--',$cat->level).''.$cat->name;
        }

        $listArray[-1] = 'корневой уровень';
        echo CHtml::dropDownList('CatCategory[pid]', $select,
            $listArray,
            array('empty' => '(Выберите категорию)'));
        ?>

        <?php echo $form->error($model, 'pid'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 70)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'type'); ?>
        <?php echo $form->dropDownList($model, 'type', array('base' => 'Простой', 'clusterized' => 'Кластеризация')); ?>
        <?php echo $form->error($model, 'type'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'name_t'); ?>
        <?php echo $form->textField($model, 'name_t', array('size' => 60, 'maxlength' => 70)); ?>
        <?php echo $form->error($model, 'name_t'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'text'); ?>
        <?php

        echo '<div style="text-align:right;">';
        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Расставить изображения',
            'type' => 'primary',
            'size' => 'mini',
            'url' => array('catCategory/tidyPost', 'id' => $model->id)
        ));
        echo '</div>';
        ?>
        <?php
        $this->widget('begemot.extensions.ckeditor.CKEditor',
            //$this->widget('CKEditor',
            //        $this->widget('//home/atv/www/atvargo.ru/protected/extensions/ckeditor/CKEditor',
            array('model' => $model, 'attribute' => 'text', 'language' => 'ru', 'editorTemplate' => 'full',));
        ?>
        <?php echo $form->error($model, 'text'); ?>
        <?php
//        $this->widget('begemot.components.htmlClearPanel.htmlClearPanel', array('id' => 'CatCategory_text'));
        ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'seo_title'); ?>
        <?php echo $form->textField($model, 'seo_title', array('size' => 60)); ?>
        <?php echo $form->error($model, 'seo_title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'layout'); ?>
        <?php echo $form->textField($model, 'layout', array('size' => 60)); ?>
        <?php echo $form->error($model, 'layout'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'viewFile'); ?>
        <?php echo $form->textField($model, 'viewFile', array('size' => 60)); ?>
        <?php echo $form->error($model, 'viewFile'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'itemViewFile'); ?>
        <?php echo $form->textField($model, 'itemViewFile', array('size' => 60)); ?>
        <?php echo $form->error($model, 'itemViewFile'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'published'); ?>
        <?php echo $form->checkbox($model, 'published'); ?>
        <?php echo $form->error($model, 'published'); ?>
    </div>
    <br/>
    <br/>
    <br/>
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget();

    // $picturesConfig = array();
    // $configFile = Yii::getPathOfAlias('webroot').'/protected/config/catalog/categoryPictureSettings.php';
    // if (file_exists($configFile)){

    //     $picturesConfig = require($configFile);

    //     $this->widget(
    //         'application.modules.pictureBox.components.PictureBox', array(
    //         'id' => 'catalogCategory',
    //         'elementId' => $model->id,
    //         'config' => $picturesConfig,
    //             )
    //     );
    // } else{
    //     Yii::app()->user->setFlash('error','Отсутствует конфигурационный файл:'.$configFile);
    // }
    ?>

</div><!-- form -->