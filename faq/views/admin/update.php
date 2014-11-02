<?php
/* @var $this FaqController */
/* @var $model Faq */

$this->breadcrumbs=array(
	'Faqs'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';
?>

<h1><?php echo Yii::t('FaqModule.faq','Update Faq'); ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>