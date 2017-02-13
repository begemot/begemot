<?php
/* @var $this ReviewsController */
/* @var $model Reviews */

$this->breadcrumbs=array(
	'Reviews'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript("", "$('.ipopover').popover({html: true});", CClientScript::POS_READY);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#reviews-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><? echo Yii::t('ReviewsModule.msg', 'Manage Reviews');?></h1>


<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'review-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
   'afterAjaxUpdate' => "function() {  $('.ipopover').popover({html: true}); }",
	'columns'=>array(
                array(
                    'header'=>Yii::t('ReviewsModule.msg', 'Username'),
                    'value'=>'$data->name',
                ),             
               array(
                    'name'=>'type',
                    'value'=>'Reviews::itemAlias("Type",$data->type)',
                    'htmlOptions'=>array('width'=>50),
                    'filter'=>Reviews::itemAlias("Type"),
                ),
               array(
                    'header'=>Yii::t('ReviewsModule.msg', 'Review'),
                    'value'=>function($data) {
                    $this->widget('begemot.extensions.contentKit.widgets.KitPopupPart', 
                    array('defaultText'=>Yii::t('ReviewsModule.msg','Review Text'),'header'=>Yii::t('ReviewsModule.msg', 'Review'),'text'=>$data->getReviewText() )); 
                    }, 'type' => 'raw'
               ),                              
//               array(
//                    'header'=>Yii::t('ReviewsModule.msg', 'Product'),
//                    'value'=>'CHtml::link(Yii::t("ReviewsModule.msg", "Product href"),"/catalog/catItem/update/id/".CHtml::encode($data->pid))',
//                    'type'=>'raw'
//               ),
               array(
                    'name'=>'status',
                    'value'=>'Reviews::itemAlias("Status",$data->status)',
                    'htmlOptions'=>array('width'=>50),
                    'filter'=>Reviews::itemAlias("Status"),
                ),
               array(
                  'class'=>'CButtonColumn',
                                 'deleteButtonImageUrl'=>false,
                                 'buttons'=>array(
                                     'approve' => array(
                                         'label'=>Yii::t('ReviewsModule.msg', 'Approve'),
                                         'url'=>'Yii::app()->urlManager->createUrl(ReviewsModule::APPROVE_ACTION_ROUTE, array("id"=>$data->id))',
                                         'options'=>array('style'=>'margin-right: 5px;'),
                                         'visible'=>'$data->status == 0',
                                         'click'=>'function(){
                                             if(confirm("'.Yii::t('ReviewsModule.msg', 'Approve this review?').'"))
                                             {
                                                 $.post($(this).attr("href")).success(function(data){
                                                     data = $.parseJSON(data);
                                                     if(data["code"] === "success")
                                                     {
                                                         $.fn.yiiGridView.update("review-grid");
                                                     }
                                                 });
                                             }
                                             return false;
                                         }',
                                     ),
                                 ),
                                 'template'=>'{approve}{delete}',
               ),
	),
));  ?>
