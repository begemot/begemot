<?php
/* @var $this FaqCatsController */
/* @var $model FaqCats */

$this->breadcrumbs=array(
	'Faq Cats'=>array('index'),
	$model->name,
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';
?>

<h1><?php echo Yii::t('FaqModule.faq','View Cat');?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
	),
)); ?>
