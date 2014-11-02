<?php
$this->breadcrumbs=array(
	Yii::t('FaqModule.faq','Faq') =>array('/faq/admin'),
);

require Yii::getPathOfAlias('webroot').'/protected/modules/faq/views/admin/_postsMenu.php';
Yii::app()->clientScript->registerScript("", "$('.ipopover').popover();", CClientScript::POS_READY);
Yii::app()->clientScript->registerScript('search', "

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('faq-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>
<h1><? echo Yii::t('FaqModule.faq','Manage Faq'); ?></h1>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'faq-grid',
	'dataProvider'=>$model->search($cid),
	'filter'=>$model,
   'afterAjaxUpdate' => "function() {  $('.ipopover').popover(); }",
	'columns'=>array(
      'name',
		array('name'=>'question','value'=>function($data) { $this->widget('begemot.extensions.contentKit.widgets.KitPopupPart', 
      array('defaultText'=>Yii::t('FaqModule.faq','Question Text'),'header'=>Yii::t('FaqModule.faq','Question Text'),'text'=>$data->question )); }, 'type' => 'raw'),
		'create_at',
		array(
			'name'=>'answered',
			'value'=>'Faq::itemAlias("Answered",$data->answered)',
			'filter' => Faq::itemAlias("Answered"),
		),		
      array(
			'name'=>'published',
			'value'=>'Faq::itemAlias("Published",$data->published)',
			'filter' => Faq::itemAlias("Published"),
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
       array(
               'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
               "header"=>"порядок",
       ),      
	),
)); ?>
