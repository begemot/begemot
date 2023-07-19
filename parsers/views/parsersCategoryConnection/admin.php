<?php
/* @var $this ParsersCategoryConnectionController */
/* @var $model ParsersCategoryConnection */

$this->breadcrumbs=array(
	'Parsers Category Connections'=>array('index'),
	'Manage',
);

require(dirname(__FILE__).'/../menu.php');


?>

<h1>Управление соединенными категориями</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'parsers-category-connection-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'type'=>'striped bordered condensed',
	'columns'=>array(
		'id',
		'connect_name',
		'category_id',
		array(      
            'header'=>'Название категории',
            'value'=>'$data->category->name'
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
