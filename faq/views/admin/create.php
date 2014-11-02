<?php
/* @var $this FaqController */
/* @var $model Faq */
$this->breadcrumbs=array(
	'Faqs'=>array('index'),
	'Create',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';
?>

<h1><? echo Yii::t('FaqModule.faq','Create Faq'); ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>