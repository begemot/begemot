<?php
/* @var $this FaqCatsController */
/* @var $model FaqCats */

$this->breadcrumbs=array(
	'Faq Cats'=>array('index'),
	'Manage',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#faq-cats-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><? echo Yii::t('FaqModule.faq','Manage Cat'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'faq-cats-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
