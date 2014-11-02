<?php
/* @var $this FaqCatsController */
/* @var $model FaqCats */

$this->breadcrumbs=array(
	'Faq Cats'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';
?>

<h1><?php echo Yii::t('FaqModule.faq','Edit Cat'); ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>