<?php
/* @var $this SliderController */
/* @var $model Slider */

$this->breadcrumbs=array(
	'Sliders'=>array('index'),
	'Manage',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/slider/views/admin/_menu.php';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#slider-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><? echo Yii::t('SliderModule.msg','Manage Slider'); ?></h1>

<?php Yii::import('begemot.extensions.grid.EImageColumn');

 $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'test-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'type'=>'striped bordered condensed',
	'columns'=>array(
                array(
                    'class' => 'EImageColumn',
                    'htmlOptions'=>array('width'=>120),
                    'imagePathExpression' => '$data->image',
                    // Optional.
                    'emptyText' => '—',
                    'imageOptions' => array(
                        'alt' => 'no',
                        'width' => 120,
                        'height' => 120,
                    ),
                ),     
               'header',
               array(
                  'class'=>'bootstrap.widgets.TbButtonColumn',
               ),
                array(
                        'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
                        "header"=>"порядок",
                ),      

))); ?>
