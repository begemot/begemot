<?php


$this->menu = require(dirname(__FILE__) . '/../commonMenu.php');


?>

<h1>Manage Prices</h1>

<?php
$dataProvider = $model->search();
$dataProvider->setPagination(false);
$this->widget('bootstrap.widgets.TbExtendedGridView', array(
    'id' => 'prices-grid',
    'dataProvider' =>$dataProvider ,

//    'enablePagination' => false,
    'enableSorting' => false,
    'columns' => array(
        array(
            'class' => 'begemot.extensions.bootstrap.widgets.TbEditableColumn',
            'name' => 'name',


            'editable' => array(
                'type'=>'textarea',
                'url' => $this->createUrl('/priceList/prices/update'),
                'placement' => 'right',

            )
        ),
        array(
            'class' => 'begemot.extensions.bootstrap.widgets.TbEditableColumn',
            'name' => 'price',
            'headerHtmlOptions' => array('style' => 'width: 310px'),
            'editable' => array(
                'url' => $this->createUrl('/priceList/prices/update'),
                'placement' => 'right',
            )
        ),

        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{delete}'
        ),
    ),
)); ?>

<?php
/* @var $this PricesController */
/* @var $model Prices */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'prices-form',
        'action'=>'/priceList/prices/create',

        'enableAjaxValidation' => false,
    )); ?>


    <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 200)); ?>
    <?php echo $form->textField($model, 'price'); ?>
    <?php echo $form->hiddenField($model, 'catId',['value'=>$_GET['catId']]); ?>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    <a name="createForm"></a>

    <?php $this->endWidget(); ?>

</div><!-- form -->