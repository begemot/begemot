<?php
/* @var $this FaqController */
/* @var $model Faq */

$this->breadcrumbs=array(
	'Faqs'=>array('index'),
	$model->name,
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';
?>

<h1><? echo Yii::t('FaqModule.faq','View Faq'); ?></h1>

<?php
$attributes = array();
array_push($attributes,
		'id',
		'name',
      array('name'=>'email','value'=>$model->email ? $model->email : "Не указан"),
      array('name'=>'phone','value'=>$model->phone ? $model->phone : "Не указан"),
		'question',
      array('name'=>'answer','value'=>$model->answer ? $model->answer : "Нет ответа"),
      array('name'=>'answered','value'=>Faq::itemAlias("Answered", $model->answered)),
      array('name'=>'published','value'=>Faq::itemAlias("Published", $model->published)),
		'create_at',
      array('name'=>'cid', 'value'=>$model->cid == 0 ? "Модерируемые" :FaqCats::model()->findByPk($model->cid)->name)
      );

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>$attributes
)); ?>
