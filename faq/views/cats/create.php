<?php
/* @var $this FaqCatsController */
/* @var $model FaqCats */

$this->breadcrumbs=array(
	'Faq Cats'=>array('index'),
	'Create',
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';
?>

<h1><?php echo Yii::t('FaqModule.faq','Create Cat'); ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>